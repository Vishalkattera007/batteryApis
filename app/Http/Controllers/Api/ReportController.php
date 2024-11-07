<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BatteryRegModel;
use App\Models\DealerModel;
use App\Models\DistributionBatteryModel;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function generateReport(Request $request)
    {
        $dealerId = $request->input('dealerId');
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $reports = DistributionBatteryModel::where(function ($query) use ($dealerId) {
            // Apply dealer_id filter only if it's provided
            if (!empty($dealerId)) {
                $query->where('dealer_id', $dealerId);
            }
        })
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->get();

        return response()->json([
            'message' => 'Report generated successfully',
            'data' => $reports,
        ]);
    }

    public function getCustomerListByDateRange(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $customers = BatteryRegModel::whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->get();

        return response()->json([
            'message' => 'Customer list fetched successfully',
            'data' => $customers,
        ]);
    }

    public function getBatteryStatusReport(Request $request)
    {
        // Get the input parameters
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $status = $request->input('status'); // Optional, can be null
        $dealerId = $request->input('dealerId'); // Optional, can be null

        // Case 1: If dealer_id, fromDate, and toDate are provided
        if ($dealerId !== null && $fromDate !== null && $toDate !== null) {
            $batteries = DistributionBatteryModel::where('dealer_id', $dealerId)
                ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                ->with([
                    'batteryMast' => function ($query) {
                        // Select only the necessary columns from battery_mast
                        $query->select('serial_no', 'categoryId', 'sub_category', 'MFD', 'warranty_period');
                    },
                ])
                ->get();

            // Map the data to only return the battery_mast information
            $data = $batteries->map(function ($battery) {
                return [
                    'battery_mast' => $battery->batteryMast,
                ];
            });

            return response()->json([
                'message' => 'Battery status report generated successfully',
                'data' => $data,
            ]);
        }

        // Case 2: If only fromDate and toDate are provided (for BatteryMast data)
        elseif ($fromDate !== null && $toDate !== null && $dealerId === null) {
            $batteries = DistributionBatteryModel::whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])->get();
        }

        // Case 3: If dealer_id, status, fromDate, and toDate are provided
        if ($dealerId !== null && $status !== null && $fromDate !== null && $toDate !== null) {
            if ($status == 1) {
                // Status is 1, join distribution_battery with battery_reg and battery_mast
                $batteries = DistributionBatteryModel::query()
                    ->join('battery_reg', 'battery_reg.serialNo', '=', 'distribution_battery.specification_no') // Match serialNo
                    ->join('battery_mast', 'battery_mast.serial_no', '=', 'distribution_battery.specification_no') // Match specification_no with battery_mast
                    ->where('distribution_battery.dealer_id', $dealerId)
                    ->where('distribution_battery.status', $status) // Sold battery
                    ->whereBetween('battery_reg.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']) // Filter battery_reg date range
                    ->select('battery_mast.*', 'battery_reg.*', 'distribution_battery.dealer_id', 'distribution_battery.status as distribution_status') // Select required fields
                    ->get();
            } else {
                // Status is 0, no need to join battery_reg, just join battery_mast
                $batteries = DistributionBatteryModel::query()
                    ->join('battery_mast', 'battery_mast.serial_no', '=', 'distribution_battery.specification_no') // Match specification_no with battery_mast
                    ->where('dealer_id', $dealerId)
                    ->where('status', $status) // Unsold battery
                    ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']) // Filter by date
                    ->select('battery_mast.*', 'distribution_battery.dealer_id', 'distribution_battery.status as distribution_status') // Select required fields
                    ->get();
            }
        }

        // Return the response with the fetched data
        return response()->json([
            'message' => 'Battery status report generated successfully',
            'data' => $batteries,
        ]);
    }


    //today sales

    public function todaySales($dealerId){
        $dealer = DistributionBatteryModel::where('dealer_id', $dealerId)->get();
        if ($dealer) {
            return response()->json([
                'status' => 200,
                'data' => $dealer->pluck('created_at'),
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
            ], 404);
        }
    }

}
