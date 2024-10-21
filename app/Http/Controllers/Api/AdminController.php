<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\adminModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    //

    public function index(){
        $allAdmins = adminModel::all();
        if($allAdmins->count()>0){
            return response()->json([
                'status'=>200,
                'data'=>$allAdmins
            ],200);
        }else{
            return response()->json([
                'status'=>404,
                'message'=>"No Admin Found"
            ],404);
        }
    }


    public function create(Request $request){
        $admin = adminModel::firstOrCreate([
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
                'message' => 'Admin created successfully',
                'data' => $admin
            ], 200);
        } else {
            return response()->json([
                'status' => 409,  // 409 Conflict indicates that the resource already exists
                'message' => 'Admin already exists',
            ], 409);
        }
    }
}
