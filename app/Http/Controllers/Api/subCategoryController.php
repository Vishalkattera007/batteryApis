<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubCategoryModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class subCategoryController extends Controller
{
    /**
     * Display a listing of subcategories or a specific subcategory.
     */
    public function index($id = null)
    {
        if ($id) {
            try {
                $subcategory = SubCategoryModel::with('category')->findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => "Given SubCategory Id is not available",
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status' => Response::HTTP_OK,
                'data' => $subcategory,
            ], Response::HTTP_OK);
        } else {
            $allSubCategories = SubCategoryModel::with('category')->get();
            if ($allSubCategories->count() > 0) {

                return response()->json([
                    'status' => Response::HTTP_OK,
                    'data' => $allSubCategories->map(function ($subcategory) {
                        // Check if category exists to avoid null reference errors
                        $category_name = $subcategory->category ? $subcategory->category->name : null;
                        $subcategory_name = $subcategory->sub_category_name;

                        return [
                            "id" => $subcategory->id,
                            "category_name" => $category_name,
                            "sub_category_name" => $subcategory_name,
                        ];
                    }),
                ], 200);
            } else {
                return response()->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => "No SubCategories Found",
                ], Response::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * Store a newly created subcategory.
     */
    public function create(Request $request)
    {
        $sub_category_name = $request->sub_category_name;
        $shortcode = $request->shortCode;

        // $intoWords = explode(' ', $sub_category_name);

        // if (count($intoWords) == 1) {
        //     $shortcode = substr($intoWords[0], 0, 3);
        // } else {
        //     $shortcode = '';
        //     foreach ($intoWords as $words) {
        //         $shortcode .= substr($words, 0, 1);
        //     }
        // }

        $subcategory_check_duplicate = SubCategoryModel::where('categoryId', $request->categoryId)->where('sub_category_name', $request->sub_category_name)->first();

        if($subcategory_check_duplicate){
            return response()->json([
                'status'=>409,
                'message'=>"Subcategory already existed",
                'data'=>$subcategory_check_duplicate
            ],409);
        }

        $subcategory = SubCategoryModel::firstOrCreate([
            'categoryId' => $request->categoryId,
            'sub_category_name' => $sub_category_name,
            'shortcode' => $shortcode,
            'created_by' => "Backend Developer",
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
        $subcategory = SubCategoryModel::find($id);

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
        $subcategory = SubCategoryModel::find($id);

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
