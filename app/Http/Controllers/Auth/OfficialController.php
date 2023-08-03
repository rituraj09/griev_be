<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use App\Models\Official;  
class OfficialController extends Controller
{ 

    function login(Request $request)
    {
        $user= Official::where('email', $request->email)->orWhere('mobile',$request->email)->first();  
        $token = $user->createToken($user->email.'_Token')->plainTextToken; 
            if (!$user || !Hash::check($request->password, $user->password)) {
              
                return response()->json([
                    'status' => 404,
                    'message' =>  'These credentials do not match our records.',
                    'status' => 201,
                ]); 
            }
         
            return response()->json([
                'status' => 200,
                'user' => $user,
                'access_token' => $token,
                'usertype' => 1
            ]); 
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
