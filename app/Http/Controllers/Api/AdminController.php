<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all admins
        return response()->json(Admin::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email',
            'password' => 'required|string|min:6',
            'phone_number' => 'required|string|max:15',
        ]);

        // Create a new admin
        $admin = Admin::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'phone_number' => $validatedData['phone_number'],
            'created_by' => 'API',
        ]);

        return response()->json($admin, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Fetch an admin by ID
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        return response()->json($admin, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Fetch admin by ID
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        // Validate request
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:admin,email,' . $admin->id,
            'password' => 'sometimes|string|min:6',
            'phone_number' => 'sometimes|string|max:15',
        ]);

        // Update admin details
        $admin->update([
            'name' => $validatedData['name'] ?? $admin->name,
            'email' => $validatedData['email'] ?? $admin->email,
            'password' => isset($validatedData['password']) ? Hash::make($validatedData['password']) : $admin->password,
            'phone_number' => $validatedData['phone_number'] ?? $admin->phone_number,
            'updated_by' => 'API',
            'updated_on' => now(),
        ]);

        return response()->json($admin, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Fetch admin by ID
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        // Delete admin
        $admin->delete();

        return response()->json(['message' => 'Admin deleted successfully'], 200);
    }
}
