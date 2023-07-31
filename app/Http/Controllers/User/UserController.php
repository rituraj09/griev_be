<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use DB, Validator, Auth, Crypt, Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {  
        $userlist = User::where(['phone'=>$request->phone, 'status'=>1])->first();
        if($userlist)
        {
            
            DB::beginTransaction();
            $otp =rand(100000, 999999); 
            $user = User::find($userlist->id);
            $user->otp=$otp;
            $user->otp_sent_at=date('Y-m-d H:i:s');
            $user->save(); 
            DB::commit();
            return response()->json([
                'status' => 200,
                'message'=> 'OTP Successfully sent to your mobile no.',
            ]);
        }
        else
        {
            $validator = Validator::make($request->all(), User::$phonerules);
            if ($validator->fails()) 
            { 
                return response()->json([
                    'status' => 404,
                    'errors'=>$validator->messages(),
                ]);
            } 
            DB::beginTransaction();
            $otp =rand(100000, 999999); 
            $user = new User(); 
            $user->phone=$request->phone;
            $user->otp=$otp;
            $user->otp_sent_at=date('Y-m-d H:i:s');
            $user->created_at=date('Y-m-d H:i:s');
            $user->status=1; 
            $user->save(); 
            DB::commit();
            return response()->json([
                'status' => 200,
                'message'=> 'OTP Successfully sent to your mobile no.',
            ]);
        }
    }
    public function otp_verify(Request $request)
    {  
        $userlist = User::where(['phone'=>$request->phone, 'otp'=>$request->otp, 'status'=>1])->first();
        if($userlist)
        {
            
            DB::beginTransaction(); 
            $user = User::find($userlist->id);
            $user->otp=null;
            $user->otp_sent_at=null;
            $user->otp_verified_at=date('Y-m-d H:i:s');
            $user->save(); 
            DB::commit();
            $token = $user->createToken($user->email.'_Token')->plainTextToken; 
            return response()->json([
                'status' => 200,
                'user'=>  $user,
                'access_token'=>$token,
            ]);
        }
        else
        {
            return response()->json([
                'status' => 201,
                'errors'=> 'OTP not matching',
            ]);
        }
    }
}
