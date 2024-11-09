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
        $dealerId = $request->dealerId;
        $message = $request->message;

        // if ($status == 1) {
        //     $updateStatus = InsentiveListModel::where('dealerId', $dealerId)
        //         ->where('typeOfInsetive', $typeOfInsetive)
        //         ->update(['status' => $status]);
        // }else{
        //     $status= null;
        //     return $status;
        // }

        $insertData = InsentiveListModel::firstOrCreate([
            'typeOfInsetive' => $typeOfInsetive,
            'dealerId' => $dealerId,
            'message' => $message,
        ]);

        if ($insertData->wasRecentlyCreated) {
            return response()->json([
                'status' => 200,
                'message' => 'Incentive Enrty Done successfully',
                'data' => $insertData,
            ], 200);
        } else {
            return response()->json([
                'status' => 409, // 409 Conflict indicates that the resource already exists
                'message' => 'Incentive Record Already Exists',
            ], 409);
        }

    }

    public function updateStatus(Request $request){

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
