<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignBatteryModel;
use App\Models\BatteryRegModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AssignBatteryController extends Controller
{
    public function index($id = null)
    {

        if (!$id == null) {
            try {
                $check_battery_id = AssignBatteryModel::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'status' => 404,
                    'message' => "Given Id is not available",
                ], 404);
            }
            return response()->json([
                'status' => 200,
                'data' => $check_battery_id,
            ], 200);

        } else {
            $all_assignments = AssignBatteryModel::with(['dealer', 'category', 'subCategory'])->get();

            if ($all_assignments->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'data' => $all_assignments->map(function ($assignment) {

                        $dealer_firstName = $assignment->dealer->FirstName;
                        $dealer_lastName = $assignment->dealer->LastName;

                        $dealer_Name = $dealer_firstName . $dealer_lastName;
                        return [
                            'id' => $assignment->id,
                            'dealer_name' => $dealer_Name, // Assuming the dealer model has a 'name' attribute
                            'category_name' => $assignment->category->name, // Assuming the category model has a 'name' attribute
                            'sub_category_name' => $assignment->subCategory->sub_category_name, // Assuming the subcategory model has a 'name' attribute
                            'nof_batteries' => $assignment->nof_batteries,
                            'created_by' => $assignment->created_by,
                            'updated_by' => $assignment->updated_by,
                            'created_at' => $assignment->created_at,
                            'updated_at' => $assignment->updated_at,
                        ];
                    }),
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => "No Batteries Assignments Found",
                ], 404);
            }
        }
    }

    public function create(Request $request)
    {
        $create_battery_assign = AssignBatteryModel::firstOrCreate([
            'dealer_id' => $request->dealer_id,
            'catergory_id' => $request->catergory_id,
            'sub_category_id' => $request->sub_category_id,
            'nof_batteries' => $request->nof_batteries,
            
            'created_by' => 'Backend Developer',
        ]);

        if ($create_battery_assign->wasRecentlyCreated) {
            return response()->json([
                'status' => 200,
                'message' => 'Assignment Done Successfully',
                'data' => $create_battery_assign,
            ], 200);
        } else {
            return response()->json([
                'status' => 409, // 409 Conflict indicates that the resource already exists
                'message' => 'Already Assigned',
            ], 409);
        }
    }

    public function update(Request $request, int $id)
    {

        $update_assignment = AssignBatteryModel::find($id);
        if ($update_assignment) {
            $update_assignment->update([
                'dealer_id' => $request->dealer_id,
                'catergory_id' => $request->catergory_id,
                'sub_category_id' => $request->sub_category_id,
                'nof_batteries' => $request->nof_batteries,
                'updated_by' => 'Frontend Developer',
            ]);

            return response()->json(
                ['status' => 200,
                    'message' => $update_assignment->dealer_id . ' ' . 'Assignment Updated Successfully',
                    'data' => $update_assignment], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Assignment Not exists',
            ], 404);
        }

    }

    public function delete(Request $request, int $id)
    {
        $detele_assignement = AssignBatteryModel::find($id);

        if (!$detele_assignement) {
            return response()->json([
                'status' => 404,
                'message' => 'Oops Assignement Not Found',
            ], 404);
        } else {
            $detele_assignement->delete();
            return response()->json(
                ['status' => 200,
                    'message' => $detele_assignement->serial_no . ' ' . 'Deleted Successfully',
                    'data' => $detele_assignement], 200);
        }
    }

    public function customerList(Request $request)
{
    // Get the dealer ID from the request query parameter, if provided
    $dealerId = $request->query('dealer_id');

    if ($dealerId) {
        // Fetch assignments based on the dealer ID
        $dealerAssignments = AssignBatteryModel::where('dealer_id', $dealerId)
            ->with(['dealer', 'category', 'subCategory'])
            ->get();

        if ($dealerAssignments->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => "No assignments found for the given dealer ID",
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $dealerAssignments->map(function ($assignment) {
                $dealerName = $assignment->dealer->FirstName . ' ' . $assignment->dealer->LastName;

                return [
                    'id' => $assignment->id,
                    'dealer_name' => $dealerName,
                    'category_name' => $assignment->category->name,
                    'sub_category_name' => $assignment->subCategory->sub_category_name,
                    'nof_batteries' => $assignment->nof_batteries,
                    'created_by' => $assignment->created_by,
                    'updated_by' => $assignment->updated_by,
                    'created_at' => $assignment->created_at,
                    'updated_at' => $assignment->updated_at,
                ];
            }),
        ], 200);
    }

    // If no dealer ID is specified, fetch all assignments
    $allAssignments = AssignBatteryModel::with(['dealer', 'category', 'subCategory'])->get();

    if ($allAssignments->isEmpty()) {
        return response()->json([
            'status' => 404,
            'message' => "No Batteries Assignments Found",
        ], 404);
    }

    return response()->json([
        'status' => 200,
        'data' => $allAssignments->map(function ($assignment) {
            $dealerName = $assignment->dealer->FirstName . ' ' . $assignment->dealer->LastName;

            return [
                'id' => $assignment->id,
                'dealer_name' => $dealerName,
                'category_name' => $assignment->category->name,
                'sub_category_name' => $assignment->subCategory->sub_category_name,
                'nof_batteries' => $assignment->nof_batteries,
                'created_by' => $assignment->created_by,
                'updated_by' => $assignment->updated_by,
                'created_at' => $assignment->created_at,
                'updated_at' => $assignment->updated_at,
            ];
        }),
    ], 200);
}

public function checkCustomer(Request $request)
    {
   
        // Retrieve the customer based on the mobile number
        $customer = BatteryRegModel::where('mobileNumber', $request->mobileNumber)->first(); // Adjust the column name accordingly

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        // If customer found, return the details
        return response()->json([
            'message' => 'Customer found',
            'customer' => $customer
        ], 200);
    }


}
