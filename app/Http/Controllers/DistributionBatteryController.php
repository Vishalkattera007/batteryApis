<?php

namespace App\Http\Controllers;

use App\Models\batteryMastModel;
use App\Models\batteryRegModel;
use App\Models\categoryModel;
use App\Models\DealerModel;
use App\Models\DistributionBatteryModel;
use App\Models\subCategoryModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
                $dist_data = DistributionBatteryModel::with('dealer:FirstName,LastName,id,dealerId')->findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'status' => 404,
                    'message' => "Given Id is not available",
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'data' => [
                    'distribution' => $dist_data,
                    'dealerId' => $dist_data->dealer->dealerId ?? 'N/A',
                    'dealer_name' => $dist_data->dealer->FirstName ?? 'N/A', // to handle cases where dealer might be null
                    'dealer_LastName' => $distribution->dealer->LastName ?? 'N/A',
                ],
            ], 200);
        } else {
            $dist_data = DistributionBatteryModel::with([
                'dealer:FirstName,LastName,id,dealerId',
                'battery:categoryId,sub_category,serial_no',
                'battery.category:id,name',
                'battery.subCategory:id,sub_category_name'])->get();
            if ($dist_data->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'data' => $dist_data->map(function ($distribution) {
                        return [
                            'distribution' => $distribution,
                            // 'dealer_FirstName' => $distribution->dealer->FirstName ?? 'N/A',
                            // 'dealer_LastName' => $distribution->dealer->LastName ?? 'N/A',

                        ];
                    }),
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => "No distributions Found",
                ], 404);
            }
        }
    }

    //Find the no of remaining batteries with dealer id and status 0

    public function findRemaining($id)
    {

        $dealer = DealerModel::find($id);

        if (!$dealer) {
            return response()->json([
                'status' => 404,
                'message' => 'Dealer not found',
            ], 404);
        }
        $remainingBatteries = DistributionBatteryModel::with(['battery.category', 'battery.subCategory'])
            ->where('dealer_id', $id)
            ->where('status', '0')
            ->get(['specification_no', 'created_at']);

        if ($remainingBatteries->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No remaining batteries found for this dealer',
            ], 404);
        }

        $batteryData = $remainingBatteries->map(function ($battery) {
            return [
                'created_at' => Carbon::parse($battery->created_at)->format('Y-m-d'),
                'specification_no' => $battery->specification_no,
                'categoryName' => $battery->battery->category ? $battery->battery->category->name : null,
                'sub_categoryName' => $battery->battery->subcategory ? $battery->battery->subcategory->sub_category_name : null,
                'MFD' => $battery->battery->MFD,
                'warranty_period' => $battery->battery->warranty_period,
                'prowarranty_period' => $battery->battery->prowarranty_period,
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => $batteryData,
        ], 200);

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

                // Check in BatteryMasterModel for a matching serial number
                $batteryMaster = batteryMastModel::where('serial_no', $specification)->first();
                if ($batteryMaster) {
                    // Log the information as an array for the context
                    Log::info('Battery Master found for specification: ' . $specification, [
                        'battery_master' => $batteryMaster->toArray(),
                    ]);
                    // Update the status to 1 if a match is found
                    $batteryMaster->update(['status' => "1"]);
                } else {
                    // Log a warning if no match is found
                    Log::warning('No Battery Master found for specification: ' . $specification);
                }
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
            'created_by' => $createdBy,
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
        // ->where('status', '0')
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
            $battery = batteryMastModel::where('serial_no', $distribution->specification_no)->first();

            // Only include distribution data if battery details are found
            if ($battery) {

                // Fetch category and subcategory names
                $categoryName = categoryModel::where('id', $battery->categoryId)->value('name');
                $subCategoryName = subCategoryModel::where('id', $battery->sub_category)->first();
                

                // Fetch battery purchase date from BatteryRegModel
                $batteryReg = batteryRegModel::where('serialNo', $battery->serial_no)->first();
                $batteryPurchaseDate = $batteryReg ? $batteryReg->BPD : null;

                return [
                    'id' => $distribution->id,
                    'dealer_id' => $distribution->dealer_id,
                    'specification_no' => $distribution->specification_no,
                    'status' => $distribution->status,
                    'created_at' => $distribution->created_at,
                    'battery_details' => [
                        'id' => $battery->id,
                        'serial_no' => $battery->serial_no,
                        'categoryId' => $categoryName,
                        'sub_category' => $subCategoryName->sub_category_name,
                        'MFD' => $battery->MFD,
                        'warranty_period' => $subCategoryName->warranty_period,
                        'prowarranty_period' => $subCategoryName->prowarranty_period,
                        'created_by' => $battery->created_by,
                        'updated_by' => $battery->updated_by,
                        'created_at' => $battery->created_at,
                        'updated_at' => $battery->updated_at,
                        'battery_purchase_date' => $batteryPurchaseDate, // Include Battery Purchase Date (BPD)

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
        $batteries = batteryMastModel::where('categoryId', $categoryId)
            ->where('sub_category', $subcategoryId)
            ->where('status', "0")
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

    public function batterydistcount($dealerId)
    {
        $count = DistributionBatteryModel::where('dealer_id', $dealerId)->count();
        // Return a JSON response with the count
        return response()->json([
            'status' => 'success',
            'count' => $count,
        ], 200);

    }
}
