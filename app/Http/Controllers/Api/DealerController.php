<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\dealerModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DealerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allAdmins = dealerModel::all();
        if ($allAdmins->count() > 0) {
            return response()->json([
                'status' => 200,
                'data' => $allAdmins,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Dealer Found",
            ], 404);
        }
    }

    public function create(Request $request)
    {
        $admin = dealerModel::firstOrCreate([
            'FirstName' => $request->FirstName,
            'LastName' => $request->LastName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'firmRegNo' => $request->firmRegNo,
            'pancard' => $request->pancard,
            'profileimage' => $request->phone_number,
        ]);

        if ($admin->wasRecentlyCreated) {
            return response()->json([
                'status' => 200,
                'message' => 'Dealer created successfully',
                'data' => $admin,
            ], 200);
        } else {
            return response()->json([
                'status' => 409, // 409 Conflict indicates that the resource already exists
                'message' => 'Dealer already exists',
            ], 409);
        }
    }

    public function show($id)
    {
        $dealer = dealerModel::find($id);
        if ($dealer) {
            return response()->json([
                'status' => 200,
                'data' => $dealer,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $dealer = dealerModel::find($id);
        $FirstName = $request->FirstName;
        $LastName = $request->LastName;
        $FullName = $FirstName.''.$LastName;
        if ($dealer) {
            $dealer->update([
                'FirstName' => $FirstName,
                'LastName' => $LastName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'firmRegNo' => $request->firmRegNo,
                'pancard' => $request->pancard,
                'profileimage' => $request->phone_number,
                'updated_by'=>"Frontend Develoepr"
            ]);

            return response()->json(
                [
                    'status' => 200,
                    'message' => $dealer->$FullName . ' ' . 'Updated Successfully',
                    'data' => $dealer,
                ],
                200
            );
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Admin Not exists',
            ], 404);
        }

    }

    public function destroy($id)
    {
        $dealer = dealerModel::find($id);

        if (!$dealer) {
            return response()->json([
                'status' => 404,
                'message' => 'Dealer not found',
            ], 404);
        }

        if ($dealer) {
            $dealer->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Dealer deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete request',
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate the admin
        if (auth()->guard('dealer')->attempt($credentials)) {
            // Authentication passed
            $dealers = auth()->guard('dealer')->user();

            // Generate token for the authenticated admin
            // $token = $admin->createToken('AdminToken')->plainTextToken;

            return response()->json([
                'status' => 200,
                'message' => 'Login successful',
                'data' => [
                    'admin' => $dealers,
                    // 'token' => $token, // Include the generated token in the response
                ],
            ]);
        }
    }

    public function count()
{
    // Use the count method on the dealerModel to get the total number of dealers
    $totalDealers = dealerModel::count();

    // Return the count in a JSON response
    return response()->json([
        'status' => 200,
        'count' => $totalDealers,
    ], 200);
}

}
