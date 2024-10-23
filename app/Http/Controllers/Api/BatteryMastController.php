<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BatteryMastModel; // Ensure the model is correctly named
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BatteryMastController extends Controller
{
    public function index($id = null)
    {

        if ($id !== null) {
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
            // $all_batteries = batteryMastModel::all();
            $all_batteries = batteryMastModel::with(['category', 'subCategory'])->get();

            if ($all_batteries->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'data' => $all_batteries->map(function ($battery) {
                        $category_name = $battery->category->name; // Access category name
                        $sub_category_name = $battery->subCategory->sub_category_name; // Access sub-category name

                        return [
                            'serialNo' => $battery->serial_no,
                            'categoryName' => $category_name,
                            'subCategoryName' => $sub_category_name,
                            'warranty_period' => $battery->warranty_period,
                            'MFD' => $battery->MFD,
                            'created_by' => $battery->created_by,
                            'updated_by' => $battery->updated_by,
                        ];
                    }),
                ], 200);
            }

            // if ($all_batteries->count() > 0) {
            //     return response()->json([
            //         'status' => 200,
            //         'data' => $all_batteries,
            //     ], 200);
            // }
            else {
                return response()->json([
                    'status' => 404,
                    'message' => "No Batteries Found",
                ], 404);
            }
        }
    }

    public function create(Request $request)
    {
        $create_battery = BatteryMastModel::firstOrCreate([
            'serial_no' => $request->serial_no,
            'category' => $request->category,
            'sub_category' => $request->sub_category,
            'MFD' => $request->MFD,
            'warranty_period' => $request->warranty_period,
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
        $update_battery = BatteryMastModel::find($id);
        $manufacture_date = Carbon::parse($request->MFD);
        if ($update_battery) {
            $update_battery->update([
                'serial_no' => $request->serial_no,
                'category' => $request->category,
                'sub_category' => $request->sub_category,
                'MFD' => $manufacture_date->toDateString(),
                'warranty_period' => $request->warranty_period,
                'updated_by' => 'Frontend Developer',
            ]);

            return response()->json([
                'status' => 200,
                'message' => $update_battery->serial_no . ' Updated Successfully',
                'data' => $update_battery,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Battery Not exists',
            ], 404);
        }
    }

    public function delete(Request $request, int $id)
    {
        $delete_battery = BatteryMastModel::find($id);

        if (!$delete_battery) {
            return response()->json([
                'status' => 404,
                'message' => 'Oops Battery Not Found',
            ], 404);
        } else {
            $delete_battery->delete();
            return response()->json([
                'status' => 200,
                'message' => $delete_battery->serial_no . ' Deleted Successfully',
                'data' => $delete_battery,
            ], 200);
        }
    }
}
