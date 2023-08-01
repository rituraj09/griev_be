<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\PostGrievanceController;

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
 
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register',[ UserController::class, 'register']);
Route::post('/otp_verify',[ UserController::class, 'otp_verify']);

Route::group(['middleware'=>'api'],function(){
    Route::post('logout', [AuthController::class,'logout']); 
    Route::get('matterlist', [MasterController::class, 'getMatter']);
    Route::get('districtlist', [MasterController::class, 'getDistrict']);
    Route::get('policelist/{id}', [MasterController::class, 'getPolice']);
    Route::get('circlelist/{id}', [MasterController::class, 'getCircle']);
    Route::post('saveGrievance', [PostGrievanceController::class, 'save']); 
    Route::get('getGrievance/{id}', [PostGrievanceController::class, 'getGrievance']); 
    Route::get('getGrievanceView/{id}/{gid}', [PostGrievanceController::class, 'getGrievanceView']); 
});

