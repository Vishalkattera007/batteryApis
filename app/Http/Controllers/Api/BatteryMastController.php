<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\batteryMastModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BatteryMastController extends Controller
{
    public function index($id = null)
    {

        if (!$id == null) {
            try {
                $battery_info = batteryMastModel::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'status' => 404,
                    'message' => "Given Id is not available",
                ], 404);
            }
            return response()->json([
                'status' => 200,
                'data' => $battery_info,
            ], 200);

        } else {
            $all_batteries = batteryMastModel::all();
            if ($all_batteries->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'data' => $all_batteries,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => "No Batteries Found",
                ], 404);
            }
        }
    }

    public function create(Request $request)
    {
        $create_battery = batteryMastModel::firstOrCreate([
            'serial_no' => $request->serial_no,
            'category' => $request->category,
            'sub_category' => $request->sub_category,
            'MFD' => $request->MFD,
            'warranty_period'=>$request->warranty_period,
            'created_by' => 'Backend Developer',
        ]);

        if ($create_battery->wasRecentlyCreated) {
            return response()->json([
                'status' => 200,
                'message' => 'Battery Entry Done Successfully',
                'data' => $create_battery,
            ], 200);
        } else {
            return response()->json([
                'status' => 409, // 409 Conflict indicates that the resource already exists
                'message' => 'Battery already exists',
            ], 409);
        }
    }

    public function update(Request $request, int $id)
    {

        $update_battery = batteryMastModel::find($id);
        $manufacture_date = Carbon::parse($request->MFD);
        if ($update_battery) {
            $update_battery->update([
                'serial_no' => $request->serial_no,
                'category' => $request->category,
                'sub_category' => $request->sub_category,
                'MFD' => $manufacture_date->toDateString(),
                'warranty_period'=>$request->warranty_period,
                'updated_by' => 'Frontend Developer',
            ]);

            return response()->json(
                ['status' => 200,
                    'message' => $update_battery->serial_no . ' ' . 'Updated Successfully',
                    'data' => $update_battery], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Battery Not exists',
            ], 404);
        }

    }

    public function delete(Request $request, int $id)
    {
        $detele_battery = batteryMastModel::find($id);

        if (!$detele_battery) {
            return response()->json([
                'status' => 404,
                'message' => 'Oops Battery Not Found',
            ], 404);
        } else {
            $detele_battery->delete();
            return response()->json(
                ['status' => 200,
                    'message' => $detele_battery->serial_no . ' ' . 'Deleted Successfully',
                    'data' => $detele_battery], 200);
        }
    }

}
