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
        $validator = Validator::make($request->all(), User::$rules);
        if ($validator->fails()) 
        { 
            return response()->json([
                'status' => 201,
                'message'=>$validator->messages(),
            ]);
        } 
        DB::beginTransaction();
        $otp =rand(100000, 999999); 
        $user = new User();
        $user->name=$request->name;
        $user->phone=$request->phone;
        $user->otp=$otp;
        $user->otp_sent_at=date('Y-m-d H:i:s');
        $user->created_at=date('Y-m-d H:i:s');
        $user->status=1; 
        $user->save(); 
        DB::commit();
        return response()->json([
            'status' => 200,
            'message'=>' User Successfully Created',
        ]);

    }
}
