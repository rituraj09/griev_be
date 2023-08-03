<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\PostGrievanceController;
use App\Http\Controllers\Auth\OfficialController;
use App\Http\Controllers\Official\MovementController;
use App\Http\Middleware\UserAuthMiddleware;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/ 


// Citizen Part 
Route::post('/register',[ UserController::class, 'register']);
Route::post('/otp_verify',[ UserController::class, 'otp_verify']);
Route::group(['middleware'=>'api'],function(){
    Route::post('citizen_logout', [UserController::class,'logout'])->name('citizen_logout');; 
    Route::post('saveGrievance', [PostGrievanceController::class, 'save']); 
    Route::get('/getGrievance', [PostGrievanceController::class, 'getGrievance']); 
    Route::get('getGrievanceView/{gid}', [PostGrievanceController::class, 'getGrievanceView']); 
 });

// Official Part
Route::post('/login',[ OfficialController::class, 'login']);
Route::group(['prefix'=>'user', 'as'=>'api.', 'namespace'=>'user\Auth','middleware' => ['auth:sanctum']], function(){ 
    Route::get('checkingAuth', function(){
        return response()->json(['message'=>'You are Logged In', 'status'=>200, ], 200);  
    }); 
    Route::get('/dashboard', [OfficialController::class, 'dashboard']); 
    Route::post('/logout', [OfficialController::class, 'logout']); 
    Route::get('/getGrievanceinbox', [MovementController::class, 'getGrievanceinbox']); 
   
});


Route::get('matterlist', [MasterController::class, 'getMatter']);
Route::get('districtlist', [MasterController::class, 'getDistrict']);
Route::get('policelist/{id}', [MasterController::class, 'getPolice']);
Route::get('circlelist/{id}', [MasterController::class, 'getCircle']);


