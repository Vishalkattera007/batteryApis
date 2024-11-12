<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\BatteryMasterImport;
use App\Imports\CategoryImport;
use App\Imports\DistributionImport;
use App\Imports\SubcategoryImport;
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

        $category_id = $request->category_id;
        $subcategory_id = $request->subcategory_id;

        // Handle file import
        Excel::import(new BatteryMasterImport($category_id, $subcategory_id), $request->file('file'));

        return response()->json([
            'status' => 200,
            'message' => 'Data imported successfully'
        ], 200);

    } catch (\Exception $e) {
        // Catch any exception thrown during the import process
        Log::error('Error importing file: ' . $e->getMessage());

        // Check if the exception is a custom "duplicate entry" exception
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            return response()->json([
                'status' => 409,
                'message' => "Duplicate entry found. Kindly check."
            ], 409);
        }

        return response()->json([
            'status' => 500,
            'message' => "An error occurred while importing the file."
        ], 500);
    }
}

    public function uploadCategoryExcel(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
            ]);

            Excel::import(new CategoryImport, $request->file('file'));

            return response()->json([
                'status' => 200,
                'message' => "Category Sheet Uploaded Successfully",
            ], 200);

        } catch (QueryException $e) {

            if ($e->getCode() == 23000) {
                return response()->json([
                    'status' => 409,
                    'message' => "Duplicate Entry Found. Kindly Check",
                ], 409);
            }

            Log::error('Error importing file:' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => "An error occured while uploading the sheet",
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error importing file:' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function uploadSubCategory(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
            ]);
            $category_id = $request->category_id;

            Excel::import(new SubcategoryImport($category_id), $request->file('file'));
            // Log::info("importing data", $category_id);
            return response()->json([
                'status' => 200,
                'message' => "SubCategory Sheet Uploaded Successfully",
            ], 200);

        } catch (QueryException $e) {

            if ($e->getCode() == 23000) {
                return response()->json([
                    'status' => 409,
                    'message' => "Duplicate Entry Found. Kindly Check",
                ], 409);
            }

            Log::error('Error importing file:' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => "An error occured while uploading the sheet",
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error importing file:' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    //dist upload

    public function distuploadExcel(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
            ]);
            $dealer_id = $request->dealer_id;
            $type_dist = $request->type_dist;
            $created_by = $request->created_by;
            // $subcategory_id = $request->subcategory_id;

            Excel::import(new DistributionImport($dealer_id, $created_by, $type_dist), $request->file('file'));

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
            return response()->json(['status' => 500, 'message' => 'An error occurred while importing the file.'], 500);
        } catch (\Exception $e) {
            Log::error('Error importing file: ' . $e->getMessage());
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }


}
