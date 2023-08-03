<?php

namespace App\Http\Controllers\Official;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\Models\Official;  
use App\Models\Grievance; 
use DB, Validator, Auth, Crypt, Hash;

class MovementController extends Controller
{
    public function getGrievanceinbox()
    {  
        $id = auth('sanctum')->user()->id; 
        $official = Official::find($id);
        $district_id =  $official->assign->district_id;  
        $grievance = Grievance::join('matters', 'grievances.matter', '=', 'matters.id')
        ->join('districts', 'grievances.district_id', '=', 'districts.id')
        ->join('organisations as co', 'grievances.circle_id', '=', 'co.id')
        ->join('organisations as po', 'grievances.police_id', '=', 'po.id') 
        ->join('users', 'grievances.user_id', '=', 'users.id')
        ->select('grievances.*', 'matters.name as mattername', 'districts.name as districtname', 
        'co.name as circlename',  'po.name as policename', 'users.name as username', 
        DB::raw('CONCAT(DATE_FORMAT(grievances.created_at, "%Y"), LPAD(grievances.id, 5, "0")) AS gid'),
        DB::raw('DATE_FORMAT(grievances.created_at, "%d-%m-%Y %H:%i:%s") as created_on'))
        ->where(['grievances.district_id'=>$district_id, 'grievances.status'=>1, 'grievances.current_status'=>1])->orderBy('id','DESC')->get();
        return response()->json([
            'status' => 200,
            'grievance' => $grievance, 
        ]); 
    }
}
