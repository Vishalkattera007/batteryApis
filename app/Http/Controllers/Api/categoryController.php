<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\categoryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class categoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        $admin = categoryModel::firstOrCreate([
            'name' => $request->name,
            'created_by' => 'Backend Developer',
        ]);

        if ($admin->wasRecentlyCreated) {
            return response()->json([
                'status' => 200,
                'message' => 'Category created successfully',
                'data' => $admin,
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
                'updated_by' => 'Frontend Developer',
            ]);

            return response()->json(
                [
                    'status' => 200,
                    'message' => $categoryId->name . ' ' . 'Updated Successfully',
                    'data' => $categoryId
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
                    'data' => $categoryId
                ],
                200
            );
        }
    }
}
