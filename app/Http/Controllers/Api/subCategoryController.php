<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\subCategoryModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\CategoryModel; // Assuming categoryModel exists
use Illuminate\Http\Request;

class subCategoryController extends Controller
{
    /**
     * Display a listing of subcategories or a specific subcategory.
     */
    public function index($id = null)
    {
        if ($id !== null) {
            try {
                $subcategory = subCategoryModel::with('category')->findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'status' => 404,
                    'message' => "Given SubCategory Id is not available",
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'data' => $subcategory,
            ], 200);
        } else {
            $allSubCategories = subCategoryModel::with('category')->get();
            if ($allSubCategories->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'data' => $allSubCategories,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => "No SubCategories Found",
                ], 404);
            }
        }
    }

    /**
     * Store a newly created subcategory.
     */
    public function create(Request $request)
    {

        $subcategory = subCategoryModel::create([
            'categoryId' => $request->categoryId,
            'sub_category_name' => $request->sub_category_name,
            'created_by' => $request->created_by,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'SubCategory created successfully',
            'data' => $subcategory,
        ], 200);
    }

    /**
     * Update the specified subcategory.
     */
    public function update(Request $request, $id)
    {
        $subcategory = subCategoryModel::find($id);

        if (!$subcategory) {
            return response()->json([
                'status' => 404,
                'message' => 'SubCategory Not Found',
            ], 404);
        }

        $subcategory->update([
            'categoryId' => $request->categoryId,
            'sub_category_name' => $request->sub_category_name,
            'updated_by' => $request->updated_by,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'SubCategory updated successfully',
            'data' => $subcategory,
        ], 200);
    }

    /**
     * Remove the specified subcategory.
     */
    public function delete($id)
    {
        $subcategory = subCategoryModel::find($id);

        if (!$subcategory) {
            return response()->json([
                'status' => 404,
                'message' => 'SubCategory Not Found',
            ], 404);
        }

        $subcategory->delete();

        return response()->json([
            'status' => 200,
            'message' => 'SubCategory deleted successfully',
            'data' => $subcategory,
        ], 200);
    }
}
