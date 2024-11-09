<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DistributionBatteryModel;
use App\Models\InsentiveListModel;
use Illuminate\Http\Request; // Correct import for DB
use Illuminate\Support\Facades\DB;

class InsentiveController extends Controller
{
    public function batteryInsetive(Request $request)
    {
        $fromDate = $request->fromDate;
        $toDate = $request->toDate;
        $soldCount = $request->soldCount; //4

        $fetchData = DistributionBatteryModel::with('dealer:id,FirstName,LastName,dealerId,created_at')
            ->where('status', 1)
            ->whereBetween(DB::raw('DATE(created_at)'), [$fromDate, $toDate])
            ->select('dealer_id', DB::raw('COUNT(*) as record_count'))
            ->groupBy('dealer_id')
            ->havingRaw('COUNT(*) >= ?', [$soldCount])
            ->get();

        if ($fetchData->isNotEmpty()) {

            $uniqueData = $fetchData->pluck('dealer')->unique('id')->values();

            return response()->json([
                'status' => 200,
                'data' => $uniqueData,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No dealer eligible for incentive",
            ], 404);
        }
    }

    public function postIncetive(Request $request)
    {

        $typeOfInsetive = $request->typeOfIncetive;
        $dealerIds = $request->dealerId; // An array of dealer IDs (e.g., ["TE001", "TE003"])
        $message = $request->message;

        $responses = [];

        // Loop through each dealerId in the dealerId array
        foreach ($dealerIds as $dealerId) {
            // Check if the incentive record already exists for the current dealerId
            $insertData = InsentiveListModel::firstOrCreate([
                'typeOfInsetive' => $typeOfInsetive,
                'dealerId' => $dealerId, // Correctly passing each dealerId as a string
                'message' => $message,
            ]);

            if ($insertData->wasRecentlyCreated) {
                // If the record was successfully created
                $responses[] = [
                    'dealerId' => $dealerId,
                    'status' => 200,
                    'message' => 'Incentive Entry Done successfully',
                    'data' => $insertData,
                ];
            } else {
                // If the record already exists for the current dealer
                $responses[] = [
                    'dealerId' => $dealerId,
                    'status' => 409,
                    'message' => 'Incentive Record Already Exists',
                ];
            }
        }

        return response()->json($responses, 200);

    }

    public function updateStatus(Request $request)
    {

        $status = $request->status;
        $dealerId = $request->dealerId;
        if ($status == 1) {
            $updateStatus = InsentiveListModel::where('dealerId', $dealerId)->update(['status' => $status]);
            return response()->json([
                'status' => 200,
                'message' => 'Updated',
            ], 200);
        }
    }
}
