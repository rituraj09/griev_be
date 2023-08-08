<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use App\Models\Official;  
use DB;
class OfficialController extends Controller
{ 

    function login(Request $request)
    {
        $user = Official::with('assign:id,user_id,role,organisation_id,district_id') 
        ->with(['desig:id,name', 'assign.organisation:id,name', 'assign.district:id,name']) 
        ->where('email', $request->email)->orWhere('mobile',$request->email)->first();
         if( $user)
         {  
            $token = $user->createToken($user->email.'_Token')->plainTextToken; 
            if (!$user || !Hash::check($request->password, $user->password)) {
               
                return response()->json([ 
                    'message' =>  'These credentials do not match our records.',
                    'status' => 201,
                ]); 
            }
            DB::beginTransaction(); 
            $usr = Official::find($user->id); 
            $usr->remember_token=$token;
            $usr->save(); 
            DB::commit();
            return response()->json([
                'status' => 200,
                'user' => $user, 
                'access_token' => $token,
                'usertype' => 1
            ]); 
            
         }
         else
         {
            return response()->json([ 
                'message' =>  'These credentials do not match our records.',
                'status' => 201,
            ]); 
         }
    }
    function logout(Request $request)
    { 
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => "Logout successfully!"
        ]); 
            
    }
    function dashboard()
    {  
        $id = auth('sanctum')->user()->id; 
        $official = Official::find($id);
        $role = [];
        if ($official) {
            foreach ($official->assign as $assign) {
                $role = $assign->role; 
            }
        } else { 
        }
        return $role;
    }
}
