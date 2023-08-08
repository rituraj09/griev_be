<?php 
namespace App\Http\Controllers;  
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\District;
use App\Models\Matter;
use App\Models\Organisation; 
use App\Models\Grievance; 
use App\Helpers\Helper;
use Illuminate\Support\Str;
use DB, Validator, Auth, Crypt, Hash;

class PostGrievanceController extends Controller
{
    public function save(Request $request)
    {     
        // $user_id = auth('sanctum')->user();
        $user_id = auth('citizen')->user();
        DB::beginTransaction(); 
        $griev = new Grievance(); 
        $griev->user_id=$user_id->id;        
        $griev->matter=$request->matter;   
        $griev->subject=$request->subject;   
        $griev->story=$request->story;  
        if($request->file('file'))
        { 
            $filenameWithExt = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file')->getClientOriginalExtension();
            $fileNameToStore_file =  $user_id->id.'_'.time().'.'.$extension; 
            $filename = Helper::storeFile($request->file('file'), 'grievances/'  . str_replace('/','', $request->matter) ); 
            $griev->file = $filename;
        } 
        $griev->district_id=$request->district_id;         
        $dist =  District::where('id',$request->district_id)->first();
        $griev->circle_id=$request->circle_id;   
        $griev->police_id=$request->police_id;   
        $griev->address=$request->address;   
        $griev->status=1;   
        $griev->created_at=date('Y-m-d H:i:s'); 
        $griev->save();  
        if($request->name)
        {
            $user = User::find($user_id->id);
            $user->name=$request->name;
            $user->save();
        }
        else
        {
            $user = User::find($user_id->id);
        }
        DB::commit();
        $yr = date( 'Y'); 
        $gno = $yr .str_pad($griev->id, 5, '0', STR_PAD_LEFT);
        return response()->json([
            'status' => 200,
            'user'=>  $user,
            'usertype'=>  2,
            'token'=> $user->remember_token,
            'message'=> 'Grievance ID. '.$gno.' has been Successfully Submited to the District Commissioner Office '.$dist->name,
        ]);
    }

    public function getGrievance()
    { 
        // $decryptionKey = 'YourEncryptionKey'; 
        // $decryptedParameter = Crypt::decrypt($id, $decryptionKey);
        // return  Crypt::decrypt($decryptedParameter);
        //$decryptedParameter = Crypt::decrypt($id, $decryptionKey);
    
        $id = auth('citizen')->user()->id;
        $grievance = Grievance::join('matters', 'grievances.matter', '=', 'matters.id')
        ->join('districts', 'grievances.district_id', '=', 'districts.id')
        ->join('organisations as co', 'grievances.circle_id', '=', 'co.id')
        ->join('organisations as po', 'grievances.police_id', '=', 'po.id')
        ->select('grievances.*', 'matters.name as mattername', 'districts.name as districtname', 
        'co.name as circlename',  'po.name as policename', 
        DB::raw('CONCAT(DATE_FORMAT(grievances.created_at, "%Y"), LPAD(grievances.id, 5, "0")) AS gid'),
        DB::raw('DATE_FORMAT(grievances.created_at, "%d-%m-%Y %H:%i:%s") as created_on'))
        ->where(['user_id'=>$id, 'status'=>1])->orderBy('id','DESC')->get();
        return response()->json([
            'status' => 200,
            'grievance' => $grievance, 
        ]); 
    }

    public function getGrievanceView($gid)
    {   
        $id = auth('citizen')->user()->id;
        $grievance = Grievance::join('matters', 'grievances.matter', '=', 'matters.id')
        ->join('districts', 'grievances.district_id', '=', 'districts.id')
        ->join('organisations as co', 'grievances.circle_id', '=', 'co.id')
        ->join('organisations as po', 'grievances.police_id', '=', 'po.id')
        ->select('grievances.*', 'matters.name as mattername', 'districts.name as districtname', 
        'co.name as circlename',  'po.name as policename', 
        DB::raw('CONCAT(DATE_FORMAT(grievances.created_at, "%Y"), LPAD(grievances.id, 5, "0")) AS gid'),
        DB::raw('DATE_FORMAT(grievances.verdicted_on, "%d-%m-%Y %H:%i:%s") as verdicted_on'),
        DB::raw('DATE_FORMAT(grievances.created_at, "%d-%m-%Y %H:%i:%s") as created_on'))
        ->where(['user_id'=>$id, 'status'=>1, 'grievances.id'=>$gid])->orderBy('id','DESC')->first();
        return response()->json([
            'status' => 200,
            'grievance' => $grievance, 
        ]); 
    }
    public function download($id)
    {   
        $trans = Grievance::find($id); 
        if (!$trans) {
            abort(404);
        }
        $filePath = Helper::decryptFile($trans->file);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION); 
        $contentType = ''; 
        if (Str::lower($extension) === 'pdf') {
            $contentType = 'application/pdf';
        } elseif (Str::lower($extension) === 'jpg' || Str::lower($extension) === 'jpeg') {
           
            $contentType = 'image/jpeg';
        } 
        if ($contentType) {
            return response()->file($filePath, ['Content-Type' => $contentType]);
        } else {
            abort(400, 'Unsupported file type');
        }
    }
}
