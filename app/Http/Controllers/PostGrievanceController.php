<?php 
namespace App\Http\Controllers;  
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\District;
use App\Models\Matter;
use App\Models\PoliceStation;
use App\Models\CircleOffice; 
use App\Models\Grievance; 
use App\Helpers\Helper;
use DB, Validator, Auth, Crypt, Hash;

class PostGrievanceController extends Controller
{
    public function save(Request $request)
    {    
        DB::beginTransaction(); 
        $griev = new Grievance(); 
        $griev->user_id=$request->user_id;        
        $griev->matter=$request->matter;   
        $griev->subject=$request->subject;   
        $griev->story=$request->story;  
        if($request->file('file'))
        { 
            $filenameWithExt = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file')->getClientOriginalExtension();
            $fileNameToStore_file =  $request->user_id.'_'.time().'.'.$extension; 
            $filename = Helper::storeFile($request->file('file'), 'grievances/'  . str_replace('/','', $request->matter) ); 
            $griev->file = $filename;
        } 
        $griev->district_id=$request->district_id;   
        $griev->circle_id=$request->circle_id;   
        $griev->police_id=$request->police_id;   
        $griev->address=$request->address;   
        $griev->status=1;   
        $griev->created_at=date('Y-m-d H:i:s'); 
        $griev->save();  
        if($request->name)
        {
            $user = User::find($request->user_id);
            $user->name=$request->name;
            $user->save();
        }
        else
        {
            $user = User::find($request->user_id);
        }
        DB::commit();
        return response()->json([
            'status' => 200,
            'user'=>  $user,
            'message'=> 'Grievance Successfully Submited',
        ]);
    }

    public function getGrievance($id)
    { 
        // $decryptionKey = 'YourEncryptionKey'; 
        // $decryptedParameter = Crypt::decrypt($id, $decryptionKey);
        // return  Crypt::decrypt($decryptedParameter);
        //$decryptedParameter = Crypt::decrypt($id, $decryptionKey);
    
        $grievance = Grievance::join('matters', 'grievances.matter', '=', 'matters.id')
        ->join('districts', 'grievances.district_id', '=', 'districts.id')
        ->join('circle_offices', 'grievances.circle_id', '=', 'circle_offices.id')
        ->join('police_stations', 'grievances.police_id', '=', 'police_stations.id')
        ->select('grievances.*', 'matters.name as mattername', 'districts.name as districtname', 
        'circle_offices.name as circlename',  'police_stations.name as policename', 
        DB::raw('CONCAT(DATE_FORMAT(grievances.created_at, "%Y"), LPAD(grievances.id, 5, "0")) AS gid'),
        DB::raw('DATE_FORMAT(grievances.created_at, "%d-%m-%Y %H:%i:%s") as created_on'))
        ->where(['user_id'=>$id, 'status'=>1])->orderBy('id','DESC')->get();
        return response()->json([
            'status' => 200,
            'grievance' => $grievance, 
        ]); 
    }

    public function getGrievanceView($id, $gid)
    {   
        $grievance = Grievance::join('matters', 'grievances.matter', '=', 'matters.id')
        ->join('districts', 'grievances.district_id', '=', 'districts.id')
        ->join('circle_offices', 'grievances.circle_id', '=', 'circle_offices.id')
        ->join('police_stations', 'grievances.police_id', '=', 'police_stations.id')
        ->select('grievances.*', 'matters.name as mattername', 'districts.name as districtname', 
        'circle_offices.name as circlename',  'police_stations.name as policename', 
        DB::raw('CONCAT(DATE_FORMAT(grievances.created_at, "%Y"), LPAD(grievances.id, 5, "0")) AS gid'),
        DB::raw('DATE_FORMAT(grievances.created_at, "%d-%m-%Y %H:%i:%s") as created_on'))
        ->where(['user_id'=>$id, 'status'=>1, 'grievances.id'=>$gid])->orderBy('id','DESC')->first();
        return response()->json([
            'status' => 200,
            'grievance' => $grievance, 
        ]); 
    }
}
