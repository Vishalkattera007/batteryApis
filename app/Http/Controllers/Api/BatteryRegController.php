<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\batteryMastModel;
use App\Models\batteryRegModel;
use App\Models\categoryModel;
use App\Models\CustomerModel;
use App\Models\DealerModel;
use App\Models\DistributionBatteryModel;
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

    public function verifyandfetch(Request $request)
    {
        $bat_sp_no = $request->serialNo;

        // Search for the battery in the DistributionBatteryModel
        $match_spec_no = DistributionBatteryModel::where('specification_no', $bat_sp_no)->first();

        // Check if the battery exists
        if ($match_spec_no) {
            $spec_no = $match_spec_no->specification_no;

            // Check if the specification number matches
            if ($spec_no === $bat_sp_no) {
                // Fetch battery details from BatteryMastModel
                $match_in_battery_master = batteryMastModel::where('serial_no', $bat_sp_no)->first();

                if ($match_in_battery_master) {
                    $category_id = $match_in_battery_master->categoryId;
                    $warranty_period = $match_in_battery_master->warranty_period;
                    $prowarranty_period = $match_in_battery_master->prowarranty_period;

                    // Fetch category name
                    $fetch_cat_name = categoryModel::where('id', $category_id)->first();
                    $cat_name = $fetch_cat_name ? $fetch_cat_name->name : 'Unknown';

                    return response()->json([
                        'status' => 200,
                        'message' => 'Battery found',
                        'data' => [
                            'categoryName' => $cat_name,
                            'warranty_period' => $warranty_period,
                            'prowarranty_period'=> $prowarranty_period,
                        ],
                    ]);
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'No matching battery in master record',
                    ]);
                }
            }
        }

        // Return a response indicating no match was found
        return response()->json([
            'status' => 404,
            'message' => 'No matching battery found',
        ]);
    }

    public function create(Request $request)
    {

        Log::info('Importing row:', $request->all());

        try {

            $create_customer_master = CustomerModel::firstOrCreate([
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'phoneNumber' => $request->phoneNumber,
                'pincode' => $request->pincode,
                'created_by' => $request->created_by,
            ]);
        
            $insertedId = $create_customer_master->id;
        
            $battery_create = batteryRegModel::firstOrCreate([
                'serialNo' => $request->serialNo,
                'type' => $request->type,
                'BPD' => $request->BPD,
                'VRN' => $request->VRN,
                'warranty' => $request->warranty,
                'customer_id' => $insertedId,  // Always pass the customer ID
                'created_by' => $request->created_by,
            ]);

            // Check if the battery was recently created
            if ($battery_create->wasRecentlyCreated) {

                $bat_serialNo = $request->serialNo;
                $bat_soldBy = $request->created_by;

                $update_statusOf_batteryDist = DistributionBatteryModel::where('dealer_id', $bat_soldBy)->where('specification_no', $bat_serialNo)->update(['status' => 1]);

                $update_statusOf_batteryMaster = batteryMastModel::where('serial_no', $request->serialNo)
                    ->update(['status' => 1]);

                return response()->json([
                    'status' => 201,
                    'message' => 'Battery Registered successfully',
                    'data' => $battery_create,
                ], 201);
            } else {
                return response()->json([
                    'status' => 409,
                    'message' => 'Battery Already Registered',
                ], 409);
            }
        } catch (\Exception $e) {

            Log::error('Error Occured', ['message'=>$e->getMessage()]);
            // Catch any unexpected error and return a 500 error
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while registering the battery',
                'error' => $e->getMessage(),
            ], 500);
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

        $check_cmno = batteryRegModel::where('mobileNumber', $customer_mno)->get(['serialNo', 'type', 'firstName', 'lastName', 'mobileNumber', 'BPD', 'warranty', 'created_by']);

        if ($check_cmno->isNotEmpty()) {
            $current_date = Carbon::now();

            $battery_data = $check_cmno->map(function ($customer) use ($current_date) {
                $warranty_date = Carbon::parse($customer->warranty);
                $purchase_date = Carbon::parse($customer->BPD);

                $remaining_warranty_days = $current_date->diffInDays($warranty_date, false);

                $days_since_purchase = $purchase_date->diffInDays($current_date);

                $warranty_status = $remaining_warranty_days > 0 ? 'Valid' : 'Expired';

                return [
                    'serialNo' => $customer->serialNo,
                    'type' => $customer->type,
                    'firstName' => $customer->firstName,
                    'lastName' => $customer->lastName,
                    'mobileNumber' => $customer->mobileNumber,
                    'BPD' => $customer->BPD,
                    'warranty' => $customer->warranty,
                    'remaining_warranty_days' => $remaining_warranty_days > 0 ? round($remaining_warranty_days) : 0,
                    'days_since_purchase' => round($days_since_purchase),
                    'warranty_status' => $warranty_status,
                    'created_by' => $customer->created_by,
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

    public function count()
    {
        // Use the count method on the dealerModel to get the total number of dealers
        $totalBatteryReg = batteryRegModel::get();

        // Return the count in a JSON response
        return response()->json([
            'status' => 200,
            'count' => $totalBatteryReg,
        ], 200);
    }

    public function getDealerCustomerDetails(Request $request, int $id)
    {
        $dealer = DealerModel::find($id);

        if (!$dealer) {
            return response()->json([
                'status' => 404,
                'message' => 'Dealer not found.',
            ], 404);
        }

        // Fetch battery records created by the dealer
        $batteryRecords = batteryRegModel::where('created_by', $id)->get();

        // Collect all unique customer IDs from the battery records
        $customerIds = $batteryRecords->pluck('customer_id')->unique();

        // Fetch all customer details using the collected customer IDs
        $customers = CustomerModel::whereIn('id', $customerIds)->get();

        // Prepare an array to hold customers with battery details
        $customersWithBatteryDetails = $customers->map(function ($customer) use ($batteryRecords) {
            // Find battery records associated with this customer
            $batteriesForCustomer = $batteryRecords->where('customer_id', $customer->id);

            // If there are battery records, add the first record's details to the customer
            if ($batteriesForCustomer->isNotEmpty()) {
                $battery = $batteriesForCustomer->first();
                $customer->serialNo = $battery->serialNo;
                $customer->type = $battery->type;
                $customer->BPD = $battery->BPD;
                $customer->VRN = $battery->VRN;
                $customer->warranty = $battery->warranty;
                $customer->Acceptance = $battery->Acceptance;
            } else {
                // If no battery records, add null values
                $customer->serialNo = null;
                $customer->type = null;
                $customer->BPD = null;
                $customer->VRN = null;
                $customer->warranty = null;
                $customer->Acceptance = null;
            }

            return $customer;
        });

        // Prepare the response
        return response()->json([
            'status' => 200,
            'dealer' => $dealer,
            'customers' => $customersWithBatteryDetails,
            'message' => 'Customer details retrieved successfully.',
        ], 200);
    }

    public function Dealercount($dealerId)
    {
        $dealerRegCount = batteryRegModel::where('created_by', $dealerId)->count();

        // Return a JSON response with the count
        return response()->json([
            'status' => 'success',
            'count' => $dealerRegCount,
        ], 200);
    }

}
