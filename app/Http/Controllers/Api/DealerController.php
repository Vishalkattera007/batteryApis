<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DealerModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DealerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allAdmins = DealerModel::all();
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

        // Initialize the path variable
        $path = null;

        // Check if a file has been uploaded
        if ($request->hasFile('profileimage')) {
            $file = $request->file('profileimage');

            // Ensure the file is valid
            if ($file->isValid()) {
                // Store the file in 'storage/app/public/profilePhoto' and get the path
                $path = $file->store('profilePhoto', 'public');
            } else {
                return response()->json([
                    'status' => 422,
                    'message' => 'Invalid file upload.',
                ], 422);
            }
        }

        // Create or update the dealer
        $admin = DealerModel::firstOrNew([
            'email' => $request->email, // Use email to check for existing dealer
        ]);

        // Set the dealer attributes
        $admin->FirstName = $request->FirstName;
        $admin->LastName = $request->LastName;
        $admin->password = Hash::make($request->password);
        $admin->phone_number = $request->phone_number;
        $admin->address = $request->address;
        $admin->firmRegNo = $request->firmRegNo;
        $admin->pancard = $request->pancard;
        $admin->profileimage = $path; // Store the path of the uploaded image if exists

        // Save the dealer record
        $admin->save();

        // Prepare the response data
        $responseData = [
            'FirstName' => $admin->FirstName,
            'LastName' => $admin->LastName,
            'email' => $admin->email,
            'phone_number' => $admin->phone_number,
            'address' => $admin->address,
            'firmRegNo' => $admin->firmRegNo,
            'pancard' => $admin->pancard,
            'profileimage' => $admin->profileimage, // Include the profile image path
            'updated_at' => $admin->updated_at,
            'created_at' => $admin->created_at,
            'id' => $admin->id,
        ];

        // Return success response
        return response()->json([
            'status' => 200,
            'message' => 'Dealer created successfully',
            'data' => $responseData,
        ], 200);
    }

    public function show($id)
    {
        $dealer = DealerModel::find($id);
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
    // Log the incoming request data for debugging
    \Log::info($request->all());

    // Find the dealer by ID
    $dealer = DealerModel::find($id);

    if ($dealer) {
        // Initialize the profile image path with the existing image path
        $profileImagePath = $dealer->profileimage; // Use the existing image path

        // Check if a new profile image has been uploaded
        if ($request->hasFile('profileImage')) {
            // Upload and save the new profile image
            $profileImage = $request->file('profileImage');

            // Define the path where the file will be stored (relative to public directory)
            $path = 'profilePhoto'; // Directory in 'public/profilePhoto'

            // Generate a unique file name
            $fileName = time() . '_' . uniqid() . '.' . $profileImage->getClientOriginalExtension();

            // Move the uploaded file to the specified path
            $profileImage->move(public_path($path), $fileName); 

            // Update the profile image path with the relative path
            $profileImagePath = $path . '/' . $fileName; // Correctly set the path
        }

        // Prepare the update data
        $updateData = [
            'FirstName' => $request->input('FirstName'),
            'LastName' => $request->input('LastName'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'address' => $request->input('address'),
            'firmRegNo' => $request->input('firmRegNo'),
            'pancard' => $request->input('pancard'),
            'profileimage' => $profileImagePath, // Use the new path or the existing one
            'updated_by' => "Frontend Developer",
        ];

        // Hash the password only if it is provided in the request
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Log update data for debugging
        \Log::info('Update Data: ', $updateData);

        // Update the dealer's details
        $dealer->update($updateData);

        // Return success response
        return response()->json(
            [
                'status' => 200,
                'message' => $dealer->FirstName . ' ' . $dealer->LastName . ' Updated Successfully',
                'data' => $dealer,
            ],
            200
        );
    } else {
        // Dealer not found
        return response()->json([
            'status' => 404,
            'message' => 'Dealer does not exist',
        ], 404);
    }
}


    

    
    public function destroy($id)
    {
        $dealer = DealerModel::find($id);

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
        } else {
            // Authentication failed
            return response()->json([
                'status' => 401,
                'message' => 'Invalid credentials. Please check your email and password.',
            ], 401);
        }
    }
    
    public function count()
{
    // Use the count method on the dealerModel to get the total number of dealers
    $totalDealers = DealerModel::count();

    // Return the count in a JSON response
    return response()->json([
        'status' => 200,
        'count' => $totalDealers,
    ], 200);
}

}
