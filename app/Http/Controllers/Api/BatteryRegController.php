<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\batteryMastModel;
use App\Models\batteryRegModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BatteryRegController extends Controller
{
    //

    public function index($id = null)
    {

        if (!$id == null) {
            try {
                $battery_reg_id = batteryRegModel::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'status' => 404,
                    'message' => "Given Id is not available",
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'data' => $battery_reg_id,
            ], 200);
        } else {
            $battery_registrations = batteryRegModel::all();

            if ($battery_registrations->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'message' => $battery_registrations,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => "No Batteries were registered yet...",
                ], 404);
            }
        }

    }

    public function create(Request $request)
    {
        // Get the Battery Purchase Date from request
        $dateOfRegistration = $request->BPD;

        // Get the serial number from the request
        $bat_sp_no = $request->serialNo;

        // Search for the battery in the batteryMastModel
        $match_spec_no = batteryMastModel::where('serial_no', $bat_sp_no)->first();

        if ($match_spec_no) {
            // Get the warranty period from the matched battery
            $warranty_period = $match_spec_no->warranty_period;

            // Calculate the warranty end date by adding warranty period months to the registration date
            $calculated_date = Carbon::parse($dateOfRegistration)->addMonths($warranty_period);
            $warranty_date = $calculated_date->toDateString();

            try {
                // Create or find the battery registration
                $battery_create = batteryRegModel::firstOrCreate([
                    'serialNo' => $bat_sp_no, // Check for uniqueness by serial number
                ], [
                    'type' => $request->type,
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'pincode' => $request->pincode,
                    'mobileNumber' => $request->mobileNumber,
                    'BPD' => $dateOfRegistration, // Battery Purchase Date
                    'VRN' => $request->VRN, // Vehicle Registration Number
                    'warranty' => $warranty_date, // Calculated warranty date
                    'Acceptance' => $request->Acceptance,
                    'created_by' => 'Backend Developer',
                ]);

                // Check if the battery was recently created
                if ($battery_create->wasRecentlyCreated) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Battery Registered successfully',
                        'data' => $battery_create,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 409,
                        'message' => 'Battery Already Registered',
                    ], 409);
                }

            } catch (\Exception $e) {
                // Catch any unexpected error and return a 500 error
                return response()->json([
                    'status' => 500,
                    'message' => 'An error occurred while registering the battery',
                    'error' => $e->getMessage(),
                ], 500);
            }

        } else {
            // If no match is found for the serial number, return a 404 response
            return response()->json([
                'status' => 404,
                'message' => 'Serial number not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id)
    {

        $battery_update = batteryRegModel::find($id);
        $dateOfRegistration = $battery_update->BPD;
        $calculated_date = Carbon::parse($dateOfRegistration)->addMonth(12);
        $warranty_date = $calculated_date->toDateString();

        if ($battery_update) {
            $battery_update->update([
                'serialNo' => $request->serialNo,
                'type' => $request->type,
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'pincode' => $request->pincode,
                'mobileNumber' => $request->mobileNumber,
                'BPD' => $request->BPD, //Battery Purchased Date
                'VRN' => $request->VRN, //Vehicle Registarion Number
                'warranty' => $warranty_date,
                'Acceptance' => $request->Acceptance,
                'updated_by' => 'Frontend Developer',
            ]);

            return response()->json(
                ['status' => 200,
                    'message' => $battery_update->serialNo . ' ' . 'Updated Successfully',
                    'data' => $battery_update], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Battery Registration No Not exists',
            ], 404);
        }

    }

    public function delete(Request $request, int $id)
    {
        $battery_reg_id = batteryRegModel::find($id);

        if (!$battery_reg_id) {
            return response()->json([
                'status' => 404,
                'message' => 'Oops Admin Not Found',
            ], 404);
        } else {
            $battery_reg_id->delete();
            return response()->json(
                ['status' => 200,
                    'message' => $battery_reg_id->serialNo . ' ' . 'Deleted Successfully',
                    'data' => $battery_reg_id], 200);
        }
    }

    public function findCustomer(Request $request)
    {
        $customer_mno = $request->cmno;

        $check_cmno = batteryRegModel::where('mobileNumber', $customer_mno)->get(['serialNo', 'firstName', 'lastName', 'BPD', 'warranty']);

        if ($check_cmno->isNotEmpty()) {
            $current_date = Carbon::now();

            $battery_data = $check_cmno->map(function ($customer) use ($current_date) 
            {
                $warranty_date = Carbon::parse($customer->warranty);
                $purchase_date = Carbon::parse($customer->BPD);

                $remaining_warranty_days = $current_date->diffInDays($warranty_date, false);

                $days_since_purchase = $purchase_date->diffInDays($current_date);

                $warranty_status = $remaining_warranty_days > 0 ? 'Valid' : 'Expired';

                return [
                    'serialNo' => $customer->serialNo,
                    'firstName' => $customer->firstName,
                    'lastName' => $customer->lastName,
                    'BPD' => $customer->BPD,
                    'warranty' => $customer->warranty,
                    'remaining_warranty_days' => $remaining_warranty_days > 0 ? round($remaining_warranty_days) : 0,
                    'days_since_purchase' => round($days_since_purchase),
                    'warranty_status' => $warranty_status,
                ];
            });

            return response()->json([
                'status' => 200,
                'message' => 'Customer found',
                'data' => $battery_data,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Customer not found',
            ], 404);
        }
    }

}
