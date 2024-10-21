<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\dealerModel;
use Illuminate\Support\Facades\Hash;

class DealerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $allAdmins = dealerModel::all();
        if($allAdmins->count()>0){
            return response()->json([
                'status'=>200,
                'data'=>$allAdmins
            ],200);
        }else{
            return response()->json([
                'status'=>404,
                'message'=>"No Dealer Found"
            ],404);
        }
    }


    public function create(Request $request){
        $admin = dealerModel::firstOrCreate([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'phone_number'=>$request->phone_number,
            'address'=>$request->address,
            'adhar'=>$request->adhar,
            'profileimage'=>$request->phone_number
        ]);

        if ($admin->wasRecentlyCreated) {
            return response()->json([
                'status' => 200,
                'message' => 'Dealer created successfully',
                'data' => $admin
            ], 200);
        } else {
            return response()->json([
                'status' => 409,  // 409 Conflict indicates that the resource already exists
                'message' => 'Dealer already exists',
            ], 409);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
