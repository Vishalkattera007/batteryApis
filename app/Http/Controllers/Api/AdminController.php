<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\adminModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    //

    public function index($id = null)
    {
        if ($id !== null) {
            try {
                $admin = adminModel::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'status' => 404,
                    'message' => "Given Id is not available",
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'data' => $admin,
            ], 200);
        } else {
            $allAdmins = adminModel::all();
            if ($allAdmins->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'data' => $allAdmins,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => "No Admin Found",
                ], 404);
            }
        }

    }

    public function create(Request $request)
    {
        $admin = adminModel::firstOrCreate([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'adhar' => $request->adhar,
            'profileimage' => $request->phone_number,
            'created_by' => 'Backend Developer',
        ]);

        if ($admin->wasRecentlyCreated) {
            return response()->json([
                'status' => 200,
                'message' => 'Admin created successfully',
                'data' => $admin,
            ], 200);
        } else {
            return response()->json([
                'status' => 409, // 409 Conflict indicates that the resource already exists
                'message' => 'Admin already exists',
            ], 409);
        }
    }

    public function update(Request $request, int $id)
    {

        $adminId = adminModel::find($id);

        if ($adminId) {
            $adminId->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'adhar' => $request->adhar,
                'profileimage' => $request->phone_number,
                'updated_by' => 'Frontend Developer',
            ]);

            return response()->json(
                ['status' => 200,
                    'message' => $adminId->name . ' ' . 'Updated Successfully',
                    'data' => $adminId], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Admin Not exists',
            ], 404);
        }

    }

    public function delete(Request $request, int $id)
    {
        $admin_id = adminModel::find($id);

        if (!$admin_id) {
            return response()->json([
                'status' => 404,
                'message' => 'Oops Admin Not Found',
            ], 404);
        } else {
            $admin_id->delete();
            return response()->json(
                ['status' => 200,
                    'message' => $admin_id->name . ' ' . 'Deleted Successfully',
                    'data' => $admin_id], 200);
        }
    }
}
