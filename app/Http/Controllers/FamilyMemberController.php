<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\FamilyMember;

use Illuminate\Support\Facades\Http;
use App\Models\Family;
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
use Illuminate\Support\Facades\Storage;

class FamilyMemberController extends Controller
{
    public function update(Request $request, $id, $role)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time().'.'.$image->getClientOriginalExtension();
            Storage::disk('s3')->put($filename, file_get_contents($image));
            
        }
        if($role == 'family_member'){
            $member = FamilyMember::where('id', $id)->first();

            $head = Family::where('id', $member->family_id)->first();

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
                $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                    'title' => 'change for First Name',
                    'body' => $newValue,
                    'type' => 'request',
                    'image_url' => "erfheifuoe",
                    'mobile_number' => $head->head_mobile_number,
                ]);
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
                $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                    'title' => 'change for Middle Name',
                    'body' => $newValue,
                    'type' => 'request',
                    'image_url' => "erfheifuoe",
                    'mobile_number' => $head->head_mobile_number,
                ]);
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
                $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                    'title' => 'change for Last Name',
                    'body' => $newValue,
                    'type' => 'request',
                    'image_url' => "erfheifuoe",
                    'mobile_number' => $head->head_mobile_number,
                ]);
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
                $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                    'title' => 'change for Occupation',
                    'body' => $newValue,
                    'type' => 'request',
                    'image_url' => "erfheifuoe",
                    'mobile_number' => $head->head_mobile_number,
                ]);
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
                $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                    'title' => 'change for dob',
                    'body' => $newValue,
                    'type' => 'request',
                    'image_url' => "erfheifuoe",
                    'mobile_number' => $head->head_mobile_number,
                ]);
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
                $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                    'title' => 'change for Mobile Number',
                    'body' => $newValue,
                    'type' => 'request',
                    'image_url' => "erfheifuoe",
                    'mobile_number' => $head->head_mobile_number,
                ]);
            }

            if($request->has('relationship_with_head')){
                $newValue = $request->relationship_with_head;
                $oldValue = $member->relationship_with_head;
                
                $request = new ChangeRequest;

                $request->column_name = 'relationship_with_head';
                $request->old_value = $oldValue;
                $request->new_value = $newValue;
                $request->member_id = $id;
                $request->head_id = $member->family_id;
                $request->status= 'pending';
                $request->save();
                $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                    'title' => 'change for Relation',
                    'body' => $newValue,
                    'type' => 'request',
                    'image_url' => "erfheifuoe",
                    'mobile_number' => $head->head_mobile_number,
                ]);
            }

            if($request->has('address')){
                $newValue = $request->address;
                $oldValue = $member->address;
                
                $request = new ChangeRequest;

                $request->column_name = 'address';
                $request->old_value = $oldValue;
                $request->new_value = $newValue;
                $request->member_id = $id;
                $request->head_id = $member->family_id;
                $request->status= 'pending';
                $request->save();
                $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                    'title' => 'change for Address',
                    'body' => $newValue,
                    'type' => 'request',
                    'image_url' => "erfheifuoe",
                    'mobile_number' => $head->head_mobile_number,
                ]);
            }

            if($request->has('qualification')){
                $newValue = $request->qualification;
                $oldValue = $member->qualification;
                
                $request = new ChangeRequest;

                $request->column_name = 'qualification';
                $request->old_value = $oldValue;
                $request->new_value = $newValue;
                $request->member_id = $id;
                $request->head_id = $member->family_id;
                $request->status= 'pending';
                $request->save();
                $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                    'title' => 'change for Qualification',
                    'body' => $newValue,
                    'type' => 'request',
                    'image_url' => "erfheifuoe",
                    'mobile_number' => $head->head_mobile_number,
                ]);
            }
            if($request->has('marital_status')){
                $newValue = $request->marital_status;
                $oldValue = $member->marital_status;
                
                $request = new ChangeRequest;

                $request->column_name = 'marital_status';
                $request->old_value = $oldValue;
                $request->new_value = $newValue;
                $request->member_id = $id;
                $request->head_id = $member->family_id;
                $request->status= 'pending';
                $request->save();
                $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                    'title' => 'change for Marital Status',
                    'body' => $newValue,
                    'type' => 'request',
                    'image_url' => "erfheifuoe",
                    'mobile_number' => $head->head_mobile_number,
                ]);
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
                $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                    'title' => 'change for Degree',
                    'body' => $newValue,
                    'type' => 'request',
                    'image_url' => "erfheifuoe",
                    'mobile_number' => $head->head_mobile_number,
                ]);
            }

            return response()->json(['message' => 'Request saved successfully']);

        }
        if($role == 'family_head'){
            $family = Family::findOrFail($id);
            $family->head_first_name = $request->input('head_first_name');
            $family->head_middle_name = $request->input('head_middle_name');
            $family->head_last_name = $request->input('head_last_name');
            $family->head_occupation = $request->input('head_occupation');
            $family->head_mobile_number = $request->input('head_mobile_number');
            $family->head_dob = $request->input('head_dob');
            $family->address = $request->input('address');
            $family->marital_status = $request->input('marital_status');
            $family->relationship_with_head = $request->input('relationship_with_head');
            $family->qualification = $request->input('qualification');
            $family->degree = $request->input('degree');
            $family->degree = $request->input('degree');
            $family->gender = $request->input('gender');
            $family->date_of_anniversary = $request->input('date_of_anniversary');
            $family->image = $filename;
            $family->save();

            return response()->json(['message' => 'Record Updated successfully']);
        }
        
    
    }

    public function birthday($id,$role){
        $birthdayDetails = [];
        $data = [];
        $today = Carbon::today();
        if($role == 'family_head'){
            $members =  DB::table('family_members')
                                ->where('family_members.family_id', $id)
                                ->whereMonth('family_members.dob', $today->month)
                                ->whereDay('family_members.dob', $today->day)
                                ->get();
            

            // Add family members' details to the response
            foreach ($members as $member) {
                $birthdayDetails[] = [
                    'id' =>$member->id,
                    'first_name' => $member->first_name,
                    'middle_name' => $member->middle_name,
                    'last_name' => $member->last_name,
                    'dob' => $member->dob,
                ];
            }
            // print_r($data);exit;
            
        }

        if($role == 'family_member'){

            $head = FamilyMember::where('id',$id)->first('family_id');
            $head_id = $head->family_id;
            $members = FamilyMember::where('family_id', $head_id )
            ->where('id', '!=', $id)
            ->whereMonth('dob', $today->month)
            ->whereDay('dob', $today->day)
            ->get();

            $family = Family::where('id', $head_id)
            ->whereMonth('head_dob', $today->month)
            ->whereDay('head_dob', $today->day)
            ->first();

            

            // Add family members' details to the response
            foreach ($members as $member) {
                $birthdayDetails[] = [
                    'id' =>$member->id,
                    'first_name' => $member->first_name,
                    'middle_name' => $member->middle_name,
                    'last_name' => $member->last_name,
                    'dob' => $member->dob,
                ];
            }

            // Add family details to the response
            if ($family) {
                $birthdayDetails[] = [
                    'id' =>$family->id,
                    'first_name' => $family->head_first_name,
                    'middle_name' => $family->head_middle_name,
                    'last_name' => $family->head_last_name,
                    'dob' => $family->head_dob,
                ];
            }         
            
        }
        $data['status'] = 'success';
        $data['message'] = 'Today is Birthday';
        $data['data'] = $birthdayDetails;
        $status =200;
        return response()->json($data, $status);
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
