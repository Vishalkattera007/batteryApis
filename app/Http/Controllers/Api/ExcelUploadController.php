<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Imports\BatteryMasterImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;

class ExcelUploadController extends Controller
{
    public function uploadExcel(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
            ]);

            Excel::import(new BatteryMasterImport, $request->file('file'));

            return response()->json(['message' => 'Data imported successfully'], 200);
        } catch (QueryException $e) {
            // Check if the error is a duplicate entry
            if ($e->getCode() == 23000 ){
                return response()->json([
                    'message' => "Duplicate entry found kindly check"
                ], 409);
            }


            Log::error('Error importing file: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while importing the file.'], 500);
        } catch (\Exception $e) {
            Log::error('Error importing file: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
