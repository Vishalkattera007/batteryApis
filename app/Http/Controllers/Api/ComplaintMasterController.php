<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ComplaintMasterModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

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
                    'dealer:id,dealerId,FirstName,LastName,email,phone_number,profileImage,state,pincode',
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
                    'dealer:id,dealerId,FirstName,LastName,email',
                ])->get();
                if ($comlpaintsData->count() > 0) {
                    return response()->json([
                        'status' => 200,
                        'data' => $comlpaintsData,
                    ], 200);
                } else {
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

        $lastComplaint = ComplaintMasterModel::latest('id')->first(); //2
        $lastComplaintId = $lastComplaint->id ?? null; //2

        $currentDate = Carbon::now()->format('Ymd'); //20241121

        $complaintId = 'C' . $currentDate . str_pad($lastComplaintId + 1, 3, '0', STR_PAD_LEFT); //C20241121(2+1,0,0)//C202411211001

        $custmerId = $request->customer_id;
        $registered_batteryId = $request->reg_battery_id;
        $complaint = $request->complaint;
        $complaint_raised_on = $request->complaint_raised_on;
        $createdBy = $request->createdBy;
        // $complaintId = $complaintId;
        // $updatedBy = $request->updatedBy;

        try {
            $create_complaints = ComplaintMasterModel::firstOrCreate(
                [
                    'customer_id' => $custmerId,
                    'Registered_battery_id' => $registered_batteryId,
                    'complaint' => $complaint,
                ],
                [
                    'complaintId' => $complaintId,
                    'complaint_raised_on' => $complaint_raised_on,
                    'created_by' => $createdBy,
                ]
            );

            if ($create_complaints->wasRecentlyCreated) {

                return response()->json([
                    'status' => 200,
                    'message' => 'Complaint raised successfully.',
                    'data' => $create_complaints,
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

    public function update(Request $request, $id)
    {

        try {

            $complaint = ComplaintMasterModel::find($id);

            if (!$complaint) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Complaint not found.',
                ], 404);
            }
            $updatedBy = $request->updatedBy;
            $resolveStatus = $request->requestStatus;
            $resolvedOn = $request->resolvedOn;
            $complaint->update([
                'resolve_Status' => $resolveStatus,
                'resolved_By' => $updatedBy,
                'resolved_On'=>$resolvedOn,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Complaint Resolved successfully',
                'data' => $complaint,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong on the server.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }
}
