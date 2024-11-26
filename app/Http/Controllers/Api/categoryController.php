<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\categoryModel;
use App\Models\subCategoryModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class categoryController extends Controller
{

    public function index($id = null)
    {
        if ($id !== null) {
            try {
                $category = categoryModel::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'status' => 404,
                    'message' => "Given Id is not available",
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'data' => $category,
            ], 200);
        } else {
            $allCategory = categoryModel::all();
            if ($allCategory->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'data' => $allCategory,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => "No Category Found",
                ], 404);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(Request $request)
    {

        $category_name = $request->name;

        $intoWords = explode(' ', $category_name);

        if (count($intoWords) == 1) {
            $shortcode = substr($intoWords[0], 0, 3);
        } else {
            $shortcode = '';
            foreach ($intoWords as $words) {
                $shortcode .= substr($words, 0, 1);
            }
        }

        $category_duplication = categoryModel::where('name', $category_name)->first();

        if ($category_duplication) {
            return response()->json([
                'status' => 409,
                'message' => "Category already existed",
                'data' => $category_duplication,
            ], 409);
        }

        $category = categoryModel::firstOrCreate([
            'name' => $category_name,
            'created_by' => "Backend Develoepr",
            'shortcode' => $shortcode,
        ]);

        if ($category->wasRecentlyCreated) {
            return response()->json([
                'status' => 200,
                'message' => 'Category created successfully',
                'data' => [
                    'category' => $category_name,
                    'shortcode' => $shortcode,
                ],
            ], 200);
        } else {
            return response()->json([
                'status' => 409, // 409 Conflict indicates that the resource already exists
                'message' => 'Category already exists',
            ], 409);
        }
    }

    /**
     * Display the specified resource.
     */
    public function update(Request $request, int $id)
    {
        $categoryId = categoryModel::find($id);

        if ($categoryId) {
            $categoryId->update([
                'name' => $request->name,
                'updated_by' => $request->updated_by,
            ]);

            return response()->json(
                [
                    'status' => 200,
                    'message' => $categoryId->name . ' ' . 'Updated Successfully',
                    'data' => $categoryId,
                ],
                200
            );
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Category Not exists',
            ], 404);
        }
    }
    public function delete(Request $request, int $id)
    {
        $categoryId = categoryModel::find($id);

        if (!$categoryId) {
            return response()->json([
                'status' => 404,
                'message' => 'Oops Category Not Found',
            ], 404);
        } else {
            $categoryId->delete();
            return response()->json(
                [
                    'status' => 200,
                    'message' => $categoryId->name . ' ' . 'Deleted Successfully',
                    'data' => $categoryId,
                ],
                200
            );
        }
    }

    public function filterCate(int $id)
    {

        $subcategory_data = subCategoryModel::where('categoryId', $id)->get(['id', 'sub_category_name', 'shortcode']);

        if ($subcategory_data->count() > 0) {
            return response()->json([
                'status' => 200,
                'data' => $subcategory_data,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Oops No Subcategories...',
            ], 404);
        }
    }
}
