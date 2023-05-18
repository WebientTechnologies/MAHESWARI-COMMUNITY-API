<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\FamilyMember;

use App\Models\Request as ChangeRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\VerifyEmail;
use Tymon\JWTAuth\Facades\JWTAuth;

class FamilyMemberController extends Controller
{
    public function update(Request $request, $id)
    {
        $member = FamilyMember::where('id', $id)->first();

            if($request->has('first_name')){
                $newValue = $request->first_name;  
                $oldValue = $member->first_name;

                $changeRequest = new ChangeRequest;

                $changeRequest->column_name = 'first_name';
                $changeRequest->old_value = $oldValue;
                $changeRequest->new_value = $newValue;
                $changeRequest->member_id = $id;
                $changeRequest->head_id = $member->family_id;
                $changeRequest->status= 'pending';
                $changeRequest->save();
            }
            if($request->has('middle_name')){
                $newValue = $request->middle_name;
                $oldValue = $member->middle_name;

                $changeRequest = new ChangeRequest;

                $changeRequest->column_name = 'middle_name';
                $changeRequest->old_value = $oldValue;
                $changeRequest->new_value = $newValue;
                $changeRequest->member_id = $id;
                $changeRequest->head_id = $member->family_id;
                $changeRequest->status= 'pending';
                $changeRequest->save();
            }
            if($request->has('last_name')){
                $newValue = $request->last_name;
                $oldValue = $member->last_name;

                $changeRequest = new ChangeRequest;

                $changeRequest->column_name = 'last_name';
                $changeRequest->old_value = $oldValue;
                $changeRequest->new_value = $newValue;
                $changeRequest->member_id = $id;
                $changeRequest->head_id = $member->family_id;
                $changeRequest->status= 'pending';
                $changeRequest->save();
            }
            if($request->has('occupation')){
                $newValue = $request->occupation;
                $oldValue = $member->occupation;

                $changeRequest = new ChangeRequest;

                $changeRequest->column_name = 'occupation';
                $changeRequest->old_value = $oldValue;
                $changeRequest->new_value = $newValue;
                $changeRequest->member_id = $id;
                $changeRequest->head_id = $member->family_id;
                $changeRequest->status= 'pending';
                $changeRequest->save();
            }
            if($request->has('dob')){
                $newValue = $request->dob;
                $oldValue = $member->dob;

                $changeRequest = new ChangeRequest;

                $changeRequest->column_name = 'dob';
                $changeRequest->old_value = $oldValue;
                $changeRequest->new_value = $newValue;
                $changeRequest->member_id = $id;
                $changeRequest->head_id = $member->family_id;
                $changeRequest->status= 'pending';
                $changeRequest->save();
            }

            if($request->has('mobile_number')){
                $newValue = $request->mobile_number;
                $oldValue = $member->mobile_number;
                
                $request = new ChangeRequest;

                $request->column_name = 'mobile_number';
                $request->old_value = $oldValue;
                $request->new_value = $newValue;
                $request->member_id = $id;
                $changeRequest->head_id = $member->family_id;
                $request->status= 'pending';
                $request->save();
            }

            if($request->has('relationship_with_head')){
                $newValue = $request->relationship_with_head;
                $oldValue = $member->relationship_with_head;
                
                $request = new ChangeRequest;

                $request->column_name = 'relationship_with_head';
                $request->old_value = $oldValue;
                $request->new_value = $newValue;
                $request->member_id = $id;
                $changeRequest->head_id = $member->family_id;
                $request->status= 'pending';
                $request->save();
            }

            if($request->has('address')){
                $newValue = $request->address;
                $oldValue = $member->address;
                
                $request = new ChangeRequest;

                $request->column_name = 'address';
                $request->old_value = $oldValue;
                $request->new_value = $newValue;
                $request->member_id = $id;
                $changeRequest->head_id = $member->family_id;
                $request->status= 'pending';
                $request->save();
            }

            if($request->has('qualification')){
                $newValue = $request->qualification;
                $oldValue = $member->qualification;
                
                $request = new ChangeRequest;

                $request->column_name = 'qualification';
                $request->old_value = $oldValue;
                $request->new_value = $newValue;
                $request->member_id = $id;
                $changeRequest->head_id = $member->family_id;
                $request->status= 'pending';
                $request->save();
            }
            if($request->has('marital_status')){
                $newValue = $request->marital_status;
                $oldValue = $member->marital_status;
                
                $request = new ChangeRequest;

                $request->column_name = 'marital_status';
                $request->old_value = $oldValue;
                $request->new_value = $newValue;
                $request->member_id = $id;
                $changeRequest->head_id = $member->family_id;
                $request->status= 'pending';
                $request->save();
            }

            if($request->has('degree')){
                $newValue = $request->degree;
                $oldValue = $member->degree;
                
                $request = new ChangeRequest;

                $request->column_name = 'degree';
                $request->old_value = $oldValue;
                $request->new_value = $newValue;
                $request->member_id = $id;
                $changeRequest->head_id = $member->family_id;
                $request->status= 'pending';
                $request->save();
            }

            return response()->json(['message' => 'Request saved successfully']);
    
    }

    public function destroy(Request $request, $id){

        $data = [];
        try{
            
            $member = FamilyMember::find($id);
            if(empty($member)){

                $data['status'] = "Error";
                $data['message'] = "member not found.";
                $status = 401;
                return response()->json($data, $status);
                exit;
            }
            $member->delete();

            $data['status'] = "Success";
            $data['message'] = "Record deleted.";
            $status = 200;
            
            return response()->json($data, $status);
            

        } catch (Exception $e){
            $data['status'] = "Error";
            $data['message'] = $e->getMessage();
            return response()->json($data, 500);
        }
    }


}
