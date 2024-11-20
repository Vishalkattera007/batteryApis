<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ComplaintMasterModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ComplaintMasterController extends Controller
{
    //

    public function index($id = null)
    {
        if ($id !== null) {
            try {
                $complaintsDataById = ComplaintMasterModel::with([
                    'customer:firstName,lastName,phoneNumber,id',
                    'batteryReg:id,serialNo,type,modelNumber,BPD,warranty',
                    'dealer:id,dealerId,FirstName,LastName,email,phone_number,profileImage,state,pincode'
                ])->findOrFail($id);

                return response()->json([
                    'status' => 200,
                    'message' => 'Data found',
                    'data' => $complaintsDataById,
                ], 200);
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'status' => 404,
                    'message' => 'The requested ID is not available',
                ], 404);
            }
        } else {

            try {
                $comlpaintsData = ComplaintMasterModel::with([
                    'customer:firstName,lastName,phoneNumber,id',
                    'batteryReg:id,serialNo,type,modelNumber,BPD,warranty',
                    'dealer:id,dealerId,FirstName,LastName,email'
                ])->get();
                if ($comlpaintsData->count() > 0) {
                    return response()->json([
                        'status' => 200,
                        'data' => $comlpaintsData,
                    ], 200);
                }else{
                    return response()->json([
                        'status' => 404,
                        'message' => 'No Complaints Found At All',
                    ], 404);
                }

            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Somthing Wen Wrong At Server',
                ], 500);
            }

        }
    }

    public function create(Request $request)
    {
        $custmerId = $request->customer_id;
        $registered_batteryId = $request->reg_battery_id;
        $complaint = $request->complaint;
        $complaint_raised_on = $request->complaint_raised_on;
        $createdBy = $request->createdBy;
        // $updatedBy = $request->updatedBy;


        try {
            $create_complaints = ComplaintMasterModel::firstOrCreate(
                [
                    'customer_id' => $custmerId,
                    'Registered_battery_id' => $registered_batteryId,
                    'complaint' => $complaint,
                ],
                [
                    'complaint_raised_on' => $complaint_raised_on,
                    'created_by' => $createdBy,
                ]
            );
    
            if ($create_complaints->wasRecentlyCreated) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Complaint raised successfully.',
                ], 200);
            } else {
                return response()->json([
                    'status' => 409,
                    'message' => 'Duplicate entry: This complaint already exists.',
                ], 409);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong on the server.',
                'error' => $e->getMessage(), 
            ], 500);
        }
      

    }
}
