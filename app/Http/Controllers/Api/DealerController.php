<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dealer;
use Illuminate\Support\Facades\Hash;

class DealerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all dealers
        return response()->json(Dealer::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:dealer,email',
            'password' => 'required|string|min:6',
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'adhar' => 'required|string|max:255',
            'profileImage' => 'required|string', // Assuming profileImage is stored as a string (path)
        ]);

        // Create a new dealer
        $dealer = Dealer::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'phone_number' => $validatedData['phone_number'],
            'address' => $validatedData['address'],
            'adhar' => $validatedData['adhar'],
            'profileImage' => $validatedData['profileImage'],
            'created_by' => 'API',
        ]);

        return response()->json($dealer, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Fetch a dealer by ID
        $dealer = Dealer::find($id);

        if (!$dealer) {
            return response()->json(['message' => 'Dealer not found'], 404);
        }

        return response()->json($dealer, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Fetch dealer by ID
        $dealer = Dealer::find($id);

        if (!$dealer) {
            return response()->json(['message' => 'Dealer not found'], 404);
        }

        // Validate request
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:dealer,email,' . $dealer->id,
            'password' => 'sometimes|string|min:6',
            'phone_number' => 'sometimes|string|max:15',
            'address' => 'sometimes|string|max:255',
            'adhar' => 'sometimes|string|max:255',
            'profileImage' => 'sometimes|string', // Assuming profileImage is a string path
        ]);

        // Update dealer details
        $dealer->update([
            'name' => $validatedData['name'] ?? $dealer->name,
            'email' => $validatedData['email'] ?? $dealer->email,
            'password' => isset($validatedData['password']) ? Hash::make($validatedData['password']) : $dealer->password,
            'phone_number' => $validatedData['phone_number'] ?? $dealer->phone_number,
            'address' => $validatedData['address'] ?? $dealer->address,
            'adhar' => $validatedData['adhar'] ?? $dealer->adhar,
            'profileImage' => $validatedData['profileImage'] ?? $dealer->profileImage,
            'updated_by' => 'API',
            'updated_on' => now(),
        ]);

        return response()->json($dealer, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Fetch dealer by ID
        $dealer = Dealer::find($id);

        if (!$dealer) {
            return response()->json(['message' => 'Dealer not found'], 404);
        }

        // Delete dealer
        $dealer->delete();

        return response()->json(['message' => 'Dealer deleted successfully'], 200);
    }
}
