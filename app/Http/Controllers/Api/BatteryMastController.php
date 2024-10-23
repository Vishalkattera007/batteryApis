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
    if ($id != null) {
        try {
            // Fetch battery info with category and subCategory relationships
            $battery_info = BatteryMastModel::with(['category', 'subCategory'])->findOrFail($id);

            return response()->json([
                'status' => 200,
                'data' => [
                    'id' => $battery_info->id,
                    'serial_no' => $battery_info->serial_no,
                    'category_id' => $battery_info->category ? $battery_info->category->id : null, // Include category ID
                    'category_name' => $battery_info->category ? $battery_info->category->name : 'N/A', // Handle null case
                    'sub_category' => $battery_info->subCategory ? $battery_info->subCategory->sub_category_name : 'N/A', // Handle null case
                    'MFD' => $battery_info->MFD,
                    'warranty_period' => $battery_info->warranty_period,
                ],
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => "Given Id is not available",
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => "An error occurred: " . $e->getMessage(),
            ], 500);
        }
    } else {
        // Fetch all batteries with category and subCategory relationships
        $all_batteries = BatteryMastModel::with(['category', 'subCategory'])->get();

        if ($all_batteries->count() > 0) {
            $batteries_data = $all_batteries->map(function ($battery) {
                return [
                    'id' => $battery->id,
                    'serial_no' => $battery->serial_no,
                    'category_id' => $battery->category ? $battery->category->id : null, // Include category ID
                    'category_name' => $battery->category ? $battery->category->name : 'N/A', // Handle null case
                    'sub_category' => $battery->subCategory ? $battery->subCategory->sub_category_name : 'N/A', // Handle null case
                    'MFD' => $battery->MFD,
                    'warranty_period' => $battery->warranty_period,
                ];
            });

            return response()->json([
                'status' => 200,
                'data' => $batteries_data,
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

    public function count()
{
    // Use the count method on the dealerModel to get the total number of dealers
    $totalBattery = BatteryMastModel::count();

    // Return the count in a JSON response
    return response()->json([
        'status' => 200,
        'count' => $totalBattery,
    ], 200);
}
}
