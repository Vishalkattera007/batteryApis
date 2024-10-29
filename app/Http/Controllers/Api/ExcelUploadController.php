<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Imports\BatteryMasterImport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelUploadController extends Controller
{
    //
    public function uploadExcel(Request $request)
    {
        Log::info('Importing row:', $request);
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
            ]);

            Excel::import(new BatteryMasterImport, $request->file('file'));

            return response()->json(['message' => 'Data imported successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error importing file: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to import data'], 500);
        }

    }
}
