<?php

namespace App\Http\Controllers;

use App\Models\batteryMastModel;
use App\Models\categoryModel;
use App\Models\DistributionBatteryModel;
use App\Models\subCategoryModel;
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
        $specifications = $request->specification_no; // Array from frontend
        $createdBy = $request->created_by;

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
                $successfullyDistributed[] = $specification;
            } else {
                $alreadyDistributed[] = $specification;
            }
        }

        // Return response after the loop completes
        return response()->json([
            'status' => 200,
            'message' => 'Distribution process completed',
            'successfully_distributed' => $successfullyDistributed,
            'already_distributed' => $alreadyDistributed,
        ], 200);
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

    public function dealerLogin($dealer_id)
    {
        // Fetch distribution data for the dealer
        $distributions = DistributionBatteryModel::where('dealer_id', $dealer_id)
            ->where('status', '0')
            ->get();

        // Check if any distributions exist
        if ($distributions->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No Distributions Found',
            ], 404);
        }

        // Map and filter results to include only those with matching battery details
        $data = $distributions->map(function ($distribution) {
            // Find the battery details based on the specification_no
            $battery = BatteryMastModel::where('serial_no', $distribution->specification_no)->first();

            // Only include distribution data if battery details are found
            if ($battery) {

                // Fetch category and subcategory names
                $categoryName = CategoryModel::where('id', $battery->categoryId)->value('name');
                $subCategoryName = SubCategoryModel::where('id', $battery->sub_category)->value('sub_category_name');

                return [
                    'id' => $distribution->id,
                    'dealer_id' => $distribution->dealer_id,
                    'specification_no' => $distribution->specification_no,
                    'status' => $distribution->status,
                    'battery_details' => [
                        'id' => $battery->id,
                        'serial_no' => $battery->serial_no,
                        'categoryId' => $categoryName,
                        'sub_category' => $subCategoryName,
                        'MFD' => $battery->MFD,
                        'warranty_period' => $battery->warranty_period,
                        'created_by' => $battery->created_by,
                        'updated_by' => $battery->updated_by,
                        'created_at' => $battery->created_at,
                        'updated_at' => $battery->updated_at,
                    ],
                ];
            }
            // Return null if no battery details were found
            return null;
        })->filter()->values(); // Filter out null values and reindex array

        return response()->json([
            'status' => 200,
            'data' => $data,
        ], 200);
    }

    public function categorySubcategoryId($categoryId, $subcategoryId)
    {
        $batteries = BatteryMastModel::where('categoryId', $categoryId)
            ->where('sub_category', $subcategoryId)
            ->where('status', "1")
            ->get();

        // Check if any records were found
        if ($batteries->isNotEmpty()) {
            // Collect id and serial_no for each matching record
            $batteryDetails = $batteries->map(function ($battery) {
                return [
                    'id' => $battery->id,
                    'serial_no' => $battery->serial_no,
                ];
            });

            return response()->json([
                'message' => 'Category and Subcategory matched',
                'battery_details' => $batteryDetails,
            ]);
        } else {
            return response()->json([
                'message' => 'Category and Subcategory do not match',
            ], 404);
        }
    }

}
