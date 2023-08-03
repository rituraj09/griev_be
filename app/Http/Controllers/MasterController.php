<?php

namespace App\Http\Controllers;  
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\District;
use App\Models\Matter; 
use App\Models\Organisation; 
use DB, Validator, Auth, Crypt, Hash;

class MasterController extends Controller
{
    public function getMatter(){
        $matters = Matter::where('is_delete', 0)->get();
        return response()->json([
            'status' => 200,
            'matters' => $matters
        ]
        );
    }
    public function getDistrict(){
        $districts = District::where('is_delete', 0)->get();
        return response()->json([
            'status' => 200,
            'districts' => $districts
        ]
        );
    }
    public function getPolice($id){
        $police = Organisation::where(['district_id'=>$id, 'is_delete'=>0, 'office_type'=>3])->get();
        if($police)
        {
            return response()->json([
                'status' => 200,
                'police' => $police
            ]);
        }
        else
        {
            return response()->json([
                'status' => 201,
                'error' => "No records found!"
            ]);
        }
    }  
    public function getCircle($id){
        $circleoffice = Organisation::where(['district_id'=>$id, 'is_delete'=>0, 'office_type'=>2])->get();
        if($circleoffice)
        {
            return response()->json([
                'status' => 200,
                'circleoffice' => $circleoffice
            ]);
        }
        else
        {
            return response()->json([
                'status' => 201,
                'error' => "No records found!"
            ]);
        }
    }
}
