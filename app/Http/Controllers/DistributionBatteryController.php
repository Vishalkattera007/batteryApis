<?php

namespace App\Http\Controllers;

use App\Models\batteryMastModel;
use App\Models\DistributionBatteryModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DistributionBatteryController extends Controller
{

    public function find($shortcode)
    {

        $find_spec_code = batteryMastModel::where('serial_no', 'LIKE', $shortcode . '%')->get(['id', 'serial_no']);

        if ($find_spec_code->count() > 0) {
            return response()->json([
                'status' => 200,
                'count' => count($find_spec_code),
                'data' => $find_spec_code,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "not found",
            ], 404);
        }
    }

    public function index($id = null)
    {
        if ($id !== null) {
            try {
                $dist_data = DistributionBatteryModel::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'status' => 404,
                    'message' => "Given Id is not available",
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'data' => $dist_data,
            ], 200);
        } else {
            $dist_data = DistributionBatteryModel::all();
            if ($dist_data->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'data' => $dist_data,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => "No distributions Found",
                ], 404);
            }
        }
    }

    public function create(Request $request)
    {
        $dealerId = $request->dealer_id;
        $typeOfDistribution = $request->type_of_distribution;
        $specifications = $request->specification_no; // Assuming it's an array from the frontend
        $createdBy = "Backend Developer";

        $successfullyDistributed = [];
        $alreadyDistributed = [];

        foreach ($specifications as $specification) {
            $addDist = DistributionBatteryModel::firstOrCreate([
                'dealer_id' => $dealerId,
                'specification_no' => $specification,
                'type_of_distribution' => $typeOfDistribution,
                'created_by' => $createdBy,
            ]);

            if ($addDist->wasRecentlyCreated) {
                $successfullyDistributed[] = $addDist;
                return response()->json([
                    'status' => 200,
                    'message' => 'Distribution process completed',
                    'successfully_distributed' => $successfullyDistributed,
                ], 200);
            } else {
                $alreadyDistributed[] = $specifications;
                return response()->json([
                    'status' => 200,
                    'message' => 'Distribution process already completed',
                    'already_distributed' => $alreadyDistributed,
                ], 200);
            }
        }

        
    }

    public function update(Request $request, int $id)
    {

        $updatedDist = DistributionBatteryModel::find($id);

        if ($updatedDist) {
            $updatedDist->update([
                'dealer_id' => $request->dealer_id,
                'specification_no' => $request->specification_no,
                'type_of_distribution' => $request->type_of_distribution,
                'created_by' => "Frontend Developer",
            ]);

            return response()->json(
                [
                    'status' => 200,
                    'message' => $updatedDist->specification_no . ' ' . 'Updated Successfully',
                    'data' => $updatedDist,
                ],
                200
            );
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Distributions',
            ], 404);
        }

    }

    public function delete(Request $request, int $id)
    {
        $deleteDist = DistributionBatteryModel::find($id);

        if (!$deleteDist) {
            return response()->json([
                'status' => 404,
                'message' => 'Oops No Distributions Found',
            ], 404);
        } else {
            $deleteDist->delete();
            return response()->json(
                [
                    'status' => 200,
                    'message' => 'Deleted Successfully',
                    'data' => $deleteDist,
                ],
                200
            );
        }
    }
}
