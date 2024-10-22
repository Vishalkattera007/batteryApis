<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\batteryRegModel;
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
                    'message' => "Working",
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
        $battery_create = batteryRegModel::firstOrCreate([
            'serialNo' => $request->serialNo,
            'type' => $request->type,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'pincode' => $request->pincode,
            'mobileNumber' => $request->mobileNumber,
            'BPD' => $request->BPD, //Battery Purchased Date
            'VRN' => $request->VRN, //Vehicle Registarion Number
            'Acceptance' => $request->Acceptance,
            'created_by' => 'Backend Developer',
        ]);

        if ($battery_create->wasRecentlyCreated) {
            return response()->json([
                'status' => 200,
                'message' => 'Battery Registered successfully',
                'data' => $battery_create,
            ], 200);
        } else {
            return response()->json([
                'status' => 409, // 409 Conflict indicates that the resource already exists
                'message' => 'Battery Already Registered',
            ], 409);
        }
    }

    public function update(Request $request, int $id)
    {

        $battery_update = batteryRegModel::find($id);

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
}
