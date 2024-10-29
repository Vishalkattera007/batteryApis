<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\BatteryMasterImport;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExcelUploadController extends Controller
{
    public function uploadExcel(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
            ]);

            Excel::import(new BatteryMasterImport, $request->file('file'));

            return response()->json([
                'status' => 200,
                // 'data' => $request->file('file'),
                'message' => 'Data imported successfully'], 200);
        } catch (QueryException $e) {
            // Check if the error is a duplicate entry
            if ($e->getCode() == 23000) {
                return response()->json([
                    'status' => 409,
                    'message' => "Duplicate entry found kindly check",
                ], 409);
            }

            Log::error('Error importing file: ' . $e->getMessage());
            return response()->json(['status' => 500,'message' => 'An error occurred while importing the file.'], 500);
        } catch (\Exception $e) {
            Log::error('Error importing file: ' . $e->getMessage());
            return response()->json(['status' => 500,'message' => $e->getMessage()], 500);
        }
    }
}
