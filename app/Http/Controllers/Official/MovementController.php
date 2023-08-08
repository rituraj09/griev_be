<?php

namespace App\Http\Controllers\Official;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\Models\Official;  
use App\Models\Grievance; 
use App\Models\Transaction; 
use App\Models\OfficialAssign; 
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use DB, Validator, Auth, Hash;

class MovementController extends Controller
{
    public function getGrievanceinbox()
    {  
        $grievance=null; 
        $inbox=null; 
        $id = auth('sanctum')->user()->id; 
        $official = Official::find($id);
        $district_id =  $official->assign->district_id;  
        if($official->assign->role==2)
        {
            $grievance = Grievance::join('matters', 'grievances.matter', '=', 'matters.id')
            ->join('districts', 'grievances.district_id', '=', 'districts.id')
            ->join('organisations as co', 'grievances.circle_id', '=', 'co.id')
            ->join('organisations as po', 'grievances.police_id', '=', 'po.id') 
            ->join('users', 'grievances.user_id', '=', 'users.id')
            ->select('grievances.*', 'matters.name as mattername', 'districts.name as districtname', 
            'co.name as circlename',  'po.name as policename', 'users.name as username', 
            DB::raw('CONCAT(DATE_FORMAT(grievances.created_at, "%Y"), LPAD(grievances.id, 5, "0")) AS gno'),
            DB::raw('DATE_FORMAT(grievances.created_at, "%d-%m-%Y %H:%i:%s")   AS createddate'),
            DB::raw('DATE_FORMAT(grievances.created_at, "%d-%m-%Y %H:%i:%s") as created_on'))
            ->where(['grievances.district_id'=>$district_id, 'grievances.status'=>1, 'grievances.current_status'=>1])->orderBy('id','DESC')->get();
            
        } 
        if($official->assign->role==2)
        {
            $currstatus=3;
        }
        else
        {
            $currstatus=2;
        }
        $inbox = Grievance::join('matters', 'grievances.matter', '=', 'matters.id')
        ->join('districts', 'grievances.district_id', '=', 'districts.id')
        ->join('organisations as co', 'grievances.circle_id', '=', 'co.id')
        ->join('organisations as po', 'grievances.police_id', '=', 'po.id') 
        ->join('users', 'grievances.user_id', '=', 'users.id')
        ->join('transactions', 'grievances.id', '=', 'transactions.grievance_id')
        ->join('officials', 'officials.id', '=', 'transactions.from_id')
        ->join('organisations', 'organisations.id', '=', 'transactions.from_org')
        ->join('designations', 'designations.id', '=', 'officials.designation')
        ->select('grievances.*', 'matters.name as mattername', 'districts.name as districtname', 
        'co.name as circlename',  'po.name as policename', 'users.name as username', 'officials.name as officialname',
        'designations.name as desig','organisations.name as org','transactions.created_at as receiveddate',
        DB::raw('CONCAT(DATE_FORMAT(grievances.created_at, "%Y"), LPAD(grievances.id, 5, "0")) AS gno'),
        DB::raw('DATE_FORMAT(grievances.created_at, "%d-%m-%Y %H:%i:%s") as created_on'),
        DB::raw('DATE_FORMAT(CASE WHEN grievances.current_status = 1 THEN grievances.created_at ELSE transactions.created_at END, "%d-%m-%Y %H:%i:%s") as createddate'),
        DB::raw('DATE_FORMAT(transactions.created_at, "%d-%m-%Y %H:%i:%s") as created_on'))
        ->where(['grievances.district_id'=>$district_id, 'grievances.status'=>1, 'grievances.current_status'=>$currstatus,
        'transactions.to_id'=> $id,'transactions.isactive'=>1, 'transactions.status'=>1 ])->orderBy('transactions.id','DESC')->get();
            
        return response()->json([
            'status' => 200,
            'grievance' => $grievance, 
            'inbox' => $inbox, 
        ]); 
            
        
    }

    public function getgrievance($gid)
    {  
        
        $id = auth('sanctum')->user()->id; 
        $official = Official::find($id);
        $district_id =  $official->assign->district_id; 
        if($official->assign->role==2)
        {
            $grievance = Grievance::join('matters', 'grievances.matter', '=', 'matters.id')
            ->join('districts', 'grievances.district_id', '=', 'districts.id')
            ->join('organisations as co', 'grievances.circle_id', '=', 'co.id')
            ->join('organisations as po', 'grievances.police_id', '=', 'po.id')
            ->join('users', 'grievances.user_id', '=', 'users.id')
            ->select('grievances.*', 'matters.name as mattername', 'districts.name as districtname', 
            'co.name as circlename',  'po.name as policename', 'grievances.police_id as policeid', 'users.name as username',  'users.phone as userphone',
            DB::raw('CONCAT(DATE_FORMAT(grievances.created_at, "%Y"), LPAD(grievances.id, 5, "0")) AS gno'),
            DB::raw('DATE_FORMAT(grievances.created_at, "%d-%m-%Y %H:%i:%s") as created_on'))
            ->where(['grievances.district_id'=>$district_id,  'grievances.status'=>1, 'grievances.id'=>$gid ,  'grievances.current_status'=>1])->orderBy('id','DESC')->first();
            return response()->json([
                'status' => 200,
                'grievance' => $grievance 
            ]); 
           
        }
       
        return response()->json([
            'status' => 401,
            'message' => "You are not Authorized!", 
        ]); 
    } 
    public function create(Request $request)
    {       
        $id = auth('sanctum')->user()->id; 
        $official = Official::find($id);
        $district_id =  $official->assign->district_id; 

        DB::beginTransaction(); 
        $trans = new Transaction();         
        $trans->grievance_id=$request->grievanceid;   
        $trans->from_id=$id;   
        $trans->from_role=$official->assign->role;  
        $trans->from_org=$official->assign->organisation_id;  

        $officialassign = OfficialAssign::where(['organisation_id'=>$request->policeid, 'is_active'=>1, 'role'=>3])->first();
       
    
        $trans->to_id=$officialassign->user_id;   
        $trans->to_role=3;  
        $trans->to_org=$request->policeid;  
        $trans->message=$request->comment;  
        if($request->file('file'))
        { 
            $filenameWithExt = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file')->getClientOriginalExtension();
            $fileNameToStore_file =   $id.'_'.time().'.'.$extension; 
            $filename = Helper::storeFile($request->file('file'), 'transaction/'  . str_replace('/','', $id) ); 
            $trans->file = $filename;
        } 
        $trans->created_at=date('Y-m-d H:i:s');   
        $trans->save(); 
        $griev = Grievance::find($request->grievanceid);
        $griev->current_status=2;
        $griev->current_state=$request->policeid;
        $griev->save();
        DB::commit();
        return response()->json([
            'status' => 200,
            'user'=>  $official,
            'usertype'=>  1, 
            'token'=> $official->remember_token,
            'message'=> 'Grievance Successfully forwarded',
        ]);
        
    } 
    public function view($gid)
    {
         
        $grievance=null;
        $prochistory=null;
        $id = auth('sanctum')->user()->id; 
        $official = Official::find($id); 
        $district_id =  $official->assign->district_id;  
        $currstatus=0;
        if($official->assign->role==2)
        {
            $currstatus=3;
        }
        else
        {
            $currstatus=2;
        }
        $grievance = Grievance::join('matters', 'grievances.matter', '=', 'matters.id')
        ->join('districts', 'grievances.district_id', '=', 'districts.id')
        ->join('organisations as co', 'grievances.circle_id', '=', 'co.id')
        ->join('organisations as po', 'grievances.police_id', '=', 'po.id') 
        ->join('users', 'grievances.user_id', '=', 'users.id')  
        ->join('transactions', 'grievances.id', '=', 'transactions.grievance_id')  
        ->join('officials', 'officials.id', '=', 'transactions.from_id')
        ->join('organisations', 'organisations.id', '=', 'transactions.from_org')
        ->join('designations', 'designations.id', '=', 'officials.designation') 

        ->select('grievances.*', 'matters.name as mattername', 'districts.name as districtname',  'users.phone as userphone',
        'co.name as circlename',  'po.name as policename', 'users.name as username', 'transactions.to_role',
        'officials.name as officialname', 'designations.name as desig', 'organisations.name as org', 'organisations.id as org_id',  
        DB::raw('CONCAT(DATE_FORMAT(grievances.created_at, "%Y"), LPAD(grievances.id, 5, "0")) AS gno'),
        DB::raw('DATE_FORMAT(grievances.created_at, "%d-%m-%Y %H:%i:%s") as created_on'))  
        ->where(['grievances.district_id'=>$district_id, 'grievances.status'=>1, 'grievances.current_status'=>$currstatus, 'grievances.id'=>$gid , 'transactions.status'=>1, 'transactions.isactive'=>1 ,'transactions.to_id'=>$id  ])->orderBy('id','DESC')->first();
        
        $prochistory = Grievance::join('transactions', 'grievances.id', '=', 'transactions.grievance_id')  
        ->join('officials as off1', 'off1.id', '=', 'transactions.from_id')
        ->join('organisations as org1', 'org1.id', '=', 'transactions.from_org')
        ->join('designations as des1', 'des1.id', '=', 'off1.designation')
        ->join('districts', 'org1.district_id', '=', 'districts.id')

        ->join('officials as off2', 'off2.id', '=', 'transactions.to_id')
        ->join('organisations as org2', 'org2.id', '=', 'transactions.to_org')
        ->join('designations as des2', 'des2.id', '=', 'off2.designation') 

        ->select( 'transactions.*',  'districts.name as districtname', 'off1.name as officialfromname','des1.name as fromdesig','org1.name as fromorg' ,'org1.id as fromorg_id',
        'off2.name as officialtoname','des2.name as todesig','org2.name as toorg' ,'org2.id as toorg_id',
        DB::raw('DATE_FORMAT(transactions.created_at, "%d-%m-%Y %H:%i:%s") as receivedon'))->where(['grievances.id'=>$gid, 'transactions.status'=>1 ])->orderBy('id','ASC')->get();
        
        if($grievance)
        {
            return response()->json([
                'status' => 200,
                'grievance' => $grievance, 
                'history' => $prochistory, 
            ]); 
        }
        else
        {
            return response()->json([
                'status' => 401, 
                'message' =>'You are not Authorized!'
            ]); 
        }

       
         
    } 
    public function forward(Request $request)
    {      
        $id = auth('sanctum')->user()->id; 
        $official = Official::find($id);
        $district_id =  $official->assign->district_id; 
        $griev = Grievance::find($request->grievanceid);
        if($griev->current_status == 2)
        {
            DB::beginTransaction(); 
            $tranlast =Transaction::where(['grievance_id'=>$request->grievanceid, 'isactive'=>1])->first();
            if($tranlast)
            {
                $tranlast->isactive = 0;
                $tranlast->save(); 
            }
            $trans = new Transaction();         
            $trans->grievance_id=$request->grievanceid;   
            $trans->from_id=$id;   
            $trans->from_role=$official->assign->role;  
            $trans->from_org=$official->assign->organisation_id;  

            $officialassign = OfficialAssign::where(['organisation_id'=>$request->orgid, 'is_active'=>1, 'role'=>2])->first();
        
        
            $trans->to_id=$officialassign->user_id;   
            $trans->to_role=2;  
            $trans->to_org=$request->orgid;  
            $trans->message=$request->comment;  
            if($request->file('file'))
            { 
                $filenameWithExt = $request->file('file')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('file')->getClientOriginalExtension();
                $fileNameToStore_file =   $id.'_'.time().'.'.$extension; 
                $filename = Helper::storeFile($request->file('file'), 'transaction/'  . str_replace('/','', $id) ); 
                $trans->file = $filename;
            } 
            $trans->created_at=date('Y-m-d H:i:s');   
            $trans->save(); 
        
            $griev->current_status=3;
            $griev->current_state=$request->orgid;
            $griev->save();
            DB::commit();
            return response()->json([
                'status' => 200,
                'user'=>  $official,
                'usertype'=>  1, 
                'token'=> $official->remember_token,
                'message'=> 'Grievance Successfully forwarded',
            ]);
        }
        else
        {
            
    
            DB::beginTransaction(); 
            $griev->current_status= 4;
            $griev->verdict= $request->comment;
            $griev->verdicted_on=  date('Y-m-d H:i:s');  
            $griev->officer_name= $official->name;
            $griev->verdicted_by= $official->desig->name;
            $griev->save();

            $trans =Transaction::where(['grievance_id'=>$request->grievanceid, 'isactive'=>1])->first();
            $trans->isactive = 0;
            $trans->save(); 
            DB::commit();
            return response()->json([
                'status' => 200,
                'user'=>  $official,
                'usertype'=>  1, 
                'token'=> $official->remember_token,
                'message'=> 'Grievance Successfully Verdicted',
            ]);
        }
    }


    public function getGrievanceSent()
    {  
        $grievance=null;
        $id = auth('sanctum')->user()->id; 
        $official = Official::find($id);
        $district_id =  $official->assign->district_id;  
         
        if($official->assign->role==3)
        {
            $currstatus=3;
            $grievance = Grievance::join('matters', 'grievances.matter', '=', 'matters.id')
        ->join('districts', 'grievances.district_id', '=', 'districts.id')
        ->join('organisations as co', 'grievances.circle_id', '=', 'co.id')
        ->join('organisations as po', 'grievances.police_id', '=', 'po.id') 
        ->join('users', 'grievances.user_id', '=', 'users.id')
        ->join('transactions', 'grievances.id', '=', 'transactions.grievance_id')
        ->join('officials', 'officials.id', '=', 'transactions.to_id')
        ->join('organisations', 'organisations.id', '=', 'transactions.to_org')
        ->join('designations', 'designations.id', '=', 'officials.designation')
        ->select('grievances.*', 'matters.name as mattername', 'districts.name as districtname', 
        'co.name as circlename',  'po.name as policename', 'users.name as username', 'officials.name as officialname',
        'designations.name as desig','organisations.name as org','transactions.created_at as receiveddate',
        DB::raw('CONCAT(DATE_FORMAT(grievances.created_at, "%Y"), LPAD(grievances.id, 5, "0")) AS gno'),
        DB::raw('DATE_FORMAT(grievances.created_at, "%d-%m-%Y %H:%i:%s") as created_on'),
        DB::raw('DATE_FORMAT(CASE WHEN grievances.current_status = 1 THEN grievances.created_at ELSE transactions.created_at END, "%d-%m-%Y %H:%i:%s") as createddate'),
        DB::raw('DATE_FORMAT(transactions.created_at, "%d-%m-%Y %H:%i:%s") as created_on'))
        ->where(['grievances.district_id'=>$district_id, 'grievances.status'=>1, 
        'transactions.from_id'=> $id, 'transactions.status'=>1 ])->whereIn('grievances.current_status',[3,4])->orderBy('transactions.id','DESC')->get();
            
        }
        else
        {
            $currstatus=2;
            $grievance = Grievance::join('matters', 'grievances.matter', '=', 'matters.id')
            ->join('districts', 'grievances.district_id', '=', 'districts.id')
            ->join('organisations as co', 'grievances.circle_id', '=', 'co.id')
            ->join('organisations as po', 'grievances.police_id', '=', 'po.id') 
            ->join('users', 'grievances.user_id', '=', 'users.id')
            ->join('transactions', 'grievances.id', '=', 'transactions.grievance_id')
            ->join('officials', 'officials.id', '=', 'transactions.to_id')
            ->join('organisations', 'organisations.id', '=', 'transactions.to_org')
            ->join('designations', 'designations.id', '=', 'officials.designation')
            ->select('grievances.*', 'matters.name as mattername', 'districts.name as districtname', 
            'co.name as circlename',  'po.name as policename', 'users.name as username', 'officials.name as officialname',
            'designations.name as desig','organisations.name as org','transactions.created_at as receiveddate',
            DB::raw('CONCAT(DATE_FORMAT(grievances.created_at, "%Y"), LPAD(grievances.id, 5, "0")) AS gno'),
            DB::raw('DATE_FORMAT(grievances.created_at, "%d-%m-%Y %H:%i:%s") as created_on'),
            DB::raw('DATE_FORMAT(CASE WHEN grievances.current_status = 1 THEN grievances.created_at ELSE transactions.created_at END, "%d-%m-%Y %H:%i:%s") as createddate'),
            DB::raw('DATE_FORMAT(transactions.created_at, "%d-%m-%Y %H:%i:%s") as created_on'))
            ->where(['grievances.district_id'=>$district_id, 'grievances.status'=>1, 'grievances.current_status'=>$currstatus,
            'transactions.from_id'=> $id,'transactions.isactive'=>1, 'transactions.status'=>1 ])->orderBy('transactions.id','DESC')->get();
                
        }
        return response()->json([
            'status' => 200,
            'grievance' => $grievance,  
        ]);  
    }

    public function getGrievanceClosed()
    {  
        $grievance=null;
        $id = auth('sanctum')->user()->id; 
        $official = Official::find($id);
        $district_id =  $official->assign->district_id;  
        if($official->assign->role==2)
        {
            $currstatus=4;
            $grievance = Grievance::join('matters', 'grievances.matter', '=', 'matters.id')
            ->join('districts', 'grievances.district_id', '=', 'districts.id')
            ->join('organisations as co', 'grievances.circle_id', '=', 'co.id')
            ->join('organisations as po', 'grievances.police_id', '=', 'po.id') 
            ->join('users', 'grievances.user_id', '=', 'users.id') 
            ->select('grievances.*', 'matters.name as mattername', 'districts.name as districtname', 
            'co.name as circlename',  'po.name as policename', 'users.name as username',  
            DB::raw('CONCAT(DATE_FORMAT(grievances.created_at, "%Y"), LPAD(grievances.id, 5, "0")) AS gno'),
            DB::raw('DATE_FORMAT(grievances.verdicted_on, "%d-%m-%Y %H:%i:%s") as verdicted_on') )
            ->where(['grievances.district_id'=>$district_id, 'grievances.status'=>1, 'grievances.current_status'=>4
            ])->orderBy('grievances.verdicted_on','DESC')->get();
                
            
            return response()->json([
                'status' => 200,
                'grievance' => $grievance,  
            ]);  
        } 
        return response()->json([
            'status' => 401,
            'message' => "You are not Authorized!", 
        ]);
    }
    public function sentview($gid)
    {  
        $id = auth('sanctum')->user()->id; 
        $official = Official::find($id); 
        $district_id =  $official->assign->district_id;   
        $grievance = Grievance::join('matters', 'grievances.matter', '=', 'matters.id')
        ->join('districts', 'grievances.district_id', '=', 'districts.id')
        ->join('organisations as co', 'grievances.circle_id', '=', 'co.id')
        ->join('organisations as po', 'grievances.police_id', '=', 'po.id') 
        ->join('users', 'grievances.user_id', '=', 'users.id')  
        ->join('transactions', 'grievances.id', '=', 'transactions.grievance_id')  
        ->join('officials', 'officials.id', '=', 'transactions.from_id')
        ->join('organisations', 'organisations.id', '=', 'transactions.from_org')
        ->join('designations', 'designations.id', '=', 'officials.designation') 

        ->select('grievances.*', 'matters.name as mattername', 'districts.name as districtname',  'users.phone as userphone',
        'co.name as circlename',  'po.name as policename', 'users.name as username', 'transactions.to_role',
        'officials.name as officialname', 'designations.name as desig', 'organisations.name as org', 'organisations.id as org_id',  
        DB::raw('CONCAT(DATE_FORMAT(grievances.created_at, "%Y"), LPAD(grievances.id, 5, "0")) AS gno'),
        DB::raw('DATE_FORMAT(grievances.verdicted_on, "%d-%m-%Y %H:%i:%s") as verdicted_on'),
        DB::raw('DATE_FORMAT(grievances.created_at, "%d-%m-%Y %H:%i:%s") as created_on'))  
        ->where(['grievances.district_id'=>$district_id, 'grievances.status'=>1,  'grievances.id'=>$gid , 'transactions.status'=>1 , 'transactions.from_id'=>$id   ])->orderBy('id','DESC')->first();
        
        $prochistory = Grievance::join('transactions', 'grievances.id', '=', 'transactions.grievance_id')  
        ->join('officials as off1', 'off1.id', '=', 'transactions.from_id')
        ->join('organisations as org1', 'org1.id', '=', 'transactions.from_org')
        ->join('designations as des1', 'des1.id', '=', 'off1.designation')
        ->join('districts', 'org1.district_id', '=', 'districts.id')

        ->join('officials as off2', 'off2.id', '=', 'transactions.to_id')
        ->join('organisations as org2', 'org2.id', '=', 'transactions.to_org')
        ->join('designations as des2', 'des2.id', '=', 'off2.designation') 

        ->select( 'transactions.*',  'districts.name as districtname', 'off1.name as officialfromname','des1.name as fromdesig','org1.name as fromorg' ,'org1.id as fromorg_id',
        'off2.name as officialtoname','des2.name as todesig','org2.name as toorg' ,'org2.id as toorg_id',
        DB::raw('DATE_FORMAT(transactions.created_at, "%d-%m-%Y %H:%i:%s") as receivedon'))->where(['grievances.id'=>$gid, 'transactions.status'=>1 ])->orderBy('id','ASC')->get();
        

        if($grievance)
        {
            return response()->json([
                'status' => 200,
                'grievance' => $grievance, 
                'history' => $prochistory, 
            ]); 
        }
        else
        {
            return response()->json([
                'status' => 401, 
                'message' =>'You are not Authorized!'
            ]); 
        } 
         
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
    public function attachment($id)
    {     
        $trans = Transaction::find($id); 
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
