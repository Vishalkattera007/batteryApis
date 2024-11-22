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
        $allDealers = DealerModel::all();
        if ($allDealers->count() > 0) {
            return response()->json([
                'status' => 200,
                'data' => $allDealers,
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
        // Check if the email already exists
        if (DealerModel::where('email', $request->email)->exists()) {
            return response()->json([
                'status' => 422,
                'message' => 'Email already exists.',
            ], 422);
        }

        $lastDealer =DealerModel::latest('id')->first();
        $lastInsertedId = $lastDealer->id ?? null;
        $state_name = $request->state;

        $intoWords = explode(' ', $state_name);

        if (count($intoWords) == 1) {
            $shortState = substr($intoWords[0], 0, 2);
        } else {
            $shortState = '';
            foreach ($intoWords as $words) {
                $shortState .= substr($words, 0, 1);
            }
        }


        $uniqueDealerId =  $shortState.'00'.($lastInsertedId+1);

        // Initialize the path variable
        $path = null;

        // Check if a file has been uploaded
        if ($request->hasFile('profileImage')) {
            $file = $request->file('profileImage');

            // Ensure the file is valid
            if ($file->isValid()) {
                // Define the path where the image will be stored
                $path = 'uploads/profilePhoto'; // Adjust the directory as needed

                // Create the directory if it doesn't exist
                if (!file_exists(public_path($path))) {
                    mkdir(public_path($path), 0755, true);
                }

                // Generate a unique filename
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                // Move the file to the public directory
                $file->move(public_path($path), $filename);

                // Set the path for saving in the database
                $path = $path . '/' . $filename;
            } else {
                return response()->json([
                    'status' => 422,
                    'message' => 'Invalid file upload.',
                ], 422);
            }
        }

        // Create the new dealer
        $dealers = new DealerModel();

        // Set the dealer attributes
        $dealers->dealerId = $uniqueDealerId;
        $dealers->FirstName = $request->FirstName;
        $dealers->LastName = $request->LastName;
        $dealers->email = $request->email;
        $dealers->password = Hash::make($request->password);
        $dealers->phone_number = $request->phone_number;
        $dealers->address = $request->address;
        $dealers->state = $request->state;
        $dealers->pincode = $request->pincode;
        $dealers->city = $request->city;
        $dealers->latitude = $request->latitude;
        $dealers->longitude = $request->longitude;
        $dealers->bankName = $request->bankName;
        $dealers->accountNumber = $request->accountNumber;
        $dealers->IFSC = $request->ifscCode;
        $dealers->accountHolderName = $request->accountHolderName;
        $dealers->firmRegNo = $request->firmRegNo;
        $dealers->pancard = $request->pancard;
        $dealers->profileImage = $path; // Store the path of the uploaded image if exists
        $dealers->created_by = $request->created_by;

        // Save the dealer record
        $dealers->save();

        // Prepare the response data
        $responseData = [
            'FirstName' => $dealers->FirstName,
            'LastName' => $dealers->LastName,
            'email' => $dealers->email,
            'phone_number' => $dealers->phone_number,
            'address' => $dealers->address,
            'state' => $dealers->state,
            'pincode' => $dealers->pincode,
            'city' => $dealers->city,
            'latitude' => $dealers->latitude,
            'longitude' => $dealers->longitude,
            'bankName' => $dealers->bankName,
            'accountNumber' => $dealers->accountNumber,
            'IFSC' => $dealers->IFSC,
            'accountHolderName' => $dealers->accountHolderName,
            'firmRegNo' => $dealers->firmRegNo,
            'pancard' => $dealers->pancard,
            'profileImage' => asset($dealers->profileImage), // Include the full URL to the profile image
            'updated_at' => $dealers->updated_at,
            'created_at' => $dealers->created_at,
            'id' => $dealers->id,
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
        // Find the existing dealer record by ID
        $dealers = DealerModel::find($id);

        if (!$dealers) {
            return response()->json([
                'status' => 404,
                'message' => 'Dealer not found.',
            ], 404);
        }

        if ($request->hasFile('profileImage')) {
            // Upload and save the profile image
            $profileImage = $request->file('profileImage');
            $path = 'uploads/profilePhoto';
            $fileName = time() . '_' . uniqid() . '.' . $profileImage->getClientOriginalExtension();
            $profileImage->move($path, $fileName);
            $profileImagePath = $path . $fileName;
        } else {
            // If no profile image is provided, keep the existing profile image path
            $profileImagePath = $dealers->profileImage;
        }

        // Update only fields that are passed in the request
        $dealers->update([
            'FirstName' => $request->input('FirstName', $dealers->FirstName), // Use the new value or retain the old one
            'LastName' => $request->input('LastName', $dealers->LastName), // Update or retain
            'email' => $request->input('email', $dealers->email), // Update email if provided
            'phone_number' => $request->input('phone_number', $dealers->phone_number),
            'address' => $request->input('address', $dealers->address),
            'state' => $request->input('state',$dealers->state),
            'city' => $request->input('city',$dealers->city),
            'pincode' => $request->input('pincode',$dealers->pincode),
            'latitude' => $request->input('latitude',$dealers->latitude),
            'longitude' => $request->input('longitude',$dealers->longitude),
            'bankName' => $request->input('bankName', $dealers->bankName),
            'accountNumber' => $request->input('accountNumber', $dealers->accountNumber),
            'IFCS' => $request->input('IFSC', $dealers->IFCS),
            'accountHolderName	' => $request->input('accountHolderName	', $dealers->accountHolderName	),
            'firmRegNo' => $request->input('firmRegNo', $dealers->firmRegNo),
            'pancard' => $request->input('pancard', $dealers->pancard),
            'profileImage' => $profileImagePath, // Update image path or keep old one
        ]);

        // Prepare the response data
        $responseData = [
            'FirstName' => $dealers->FirstName,
            'LastName' => $dealers->LastName,
            'email' => $dealers->email,
            'phone_number' => $dealers->phone_number,
            'address' => $dealers->address,
            'state' => $dealers->state,
            'city' => $dealers->city,
            'pincode' => $dealers->pincode,
            'latitude' => $dealers->latitude,
            'longitude' => $dealers->longitude,
            'bankName' => $dealers->bankName,
            'accountNumber' => $dealers->accountNumber,
            'IFCS' => $dealers->IFCS,
            'accountHolderName' => $dealers->accountHolderName,
            'firmRegNo' => $dealers->firmRegNo,
            'pancard' => $dealers->pancard,
            'profileImage' => asset($dealers->profileImage), // Include the full URL to the profile image
            'updated_at' => $dealers->updated_at,
            'created_at' => $dealers->created_at,
            'id' => $dealers->id,
        ];

        // Return success response
        return response()->json([
            'status' => 200,
            'message' => 'Dealer updated successfully',
            'data' => $responseData,
        ], 200);
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
                    'dealer' => $dealers,
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
