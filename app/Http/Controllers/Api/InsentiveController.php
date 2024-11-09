<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DistributionBatteryModel;
use Illuminate\Support\Facades\DB; // Correct import for DB
use Illuminate\Http\Request;

class InsentiveController extends Controller
{
    public function batteryInsetive(Request $request)
    {
        $fromDate = $request->fromDate;
        $toDate = $request->toDate;
        $soldCount = $request->soldCount;//4

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
                'message' => "No dealer eligible for incentive"
            ], 404);
        }
    }
}
