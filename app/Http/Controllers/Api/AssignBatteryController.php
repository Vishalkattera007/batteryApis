<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\assignBatteryModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssignBatteryController extends Controller
{
    public function index($id = null)
    {

        if (!$id == null) {
            try {
                $check_battery_id = assignBatteryModel::findOrFail($id);
            } catch (assignBatteryModel $e) {
                return response()->json([
                    'status' => 404,
                    'message' => "Given Id is not available",
                ], 404);
            }
            return response()->json([
                'status' => 200,
                'data' => $check_battery_id,
            ], 200);

        } else {
            $all_assignments = assignBatteryModel::all();
            if ($all_assignments->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'data' => $all_assignments,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => "No Batteries Assignments Found",
                ], 404);
            }
        }
    }

    public function create(Request $request)
    {
        $create_battery_assign = assignBatteryModel::firstOrCreate([
            'dealer_id' => $request->dealer_id,
            'catergory_id' => $request->catergory_id,
            'sub_category_id' => $request->sub_category_id,
            'nof_batteries' => $request->nof_batteries,
            'created_by' => 'Backend Developer',
        ]);

        if ($create_battery_assign->wasRecentlyCreated) {
            return response()->json([
                'status' => 200,
                'message' => 'Assignment Done Successfully',
                'data' => $create_battery_assign,
            ], 200);
        } else {
            return response()->json([
                'status' => 409, // 409 Conflict indicates that the resource already exists
                'message' => 'Already Assigned',
            ], 409);
        }
    }

    public function update(Request $request, int $id)
    {

        $update_assignment = assignBatteryModel::find($id);
        if ($update_assignment) {
            $update_assignment->update([
                'dealer_id' => $request->dealer_id,
                'catergory_id' => $request->catergory_id,
                'sub_category_id' => $request->sub_category_id,
                'nof_batteries' => $request->nof_batteries,
                'updated_by' => 'Frontend Developer',
            ]);

            return response()->json(
                ['status' => 200,
                    'message' => $update_assignment->dealer_id . ' ' . 'Assignment Updated Successfully',
                    'data' => $update_assignment], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Assignment Not exists',
            ], 404);
        }

    }

    public function delete(Request $request, int $id)
    {
        $detele_assignement = assignBatteryModel::find($id);

        if (!$detele_assignement) {
            return response()->json([
                'status' => 404,
                'message' => 'Oops Assignement Not Found',
            ], 404);
        } else {
            $detele_assignement->delete();
            return response()->json(
                ['status' => 200,
                    'message' => $detele_assignement->serial_no . ' ' . 'Deleted Successfully',
                    'data' => $detele_assignement], 200);
        }
    }

}
