<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiGetDataController;
use App\Http\Controllers\ApiInsertController;
use App\Http\Controllers\ApiUpdateAndDeleteController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//api get data from database
Route::get('/get/won', [ApiGetDataController::class, 'DataWon']);
Route::get('/get/modelname', [ApiGetDataController::class, 'DataModelName']);
Route::get('/get/line', [ApiGetDataController::class, 'DataLine']);
Route::get('/get/checkmodel', [ApiGetDataController::class, 'CheckModelName']);
Route::get('/get/count/opr', [ApiGetDataController::class, 'CountInsertOpr']);
Route::get('/get/record/opr', [ApiGetDataController::class, 'GetRecordOpr']);
Route::get('/get/oprform', [ApiGetDataController::class, 'GetOprForm']);
Route::get('/get/alldata', [ApiGetDataController::class, 'GetAllData']);
Route::get('users', [ApiGetDataController::class, 'GetUsersWeb']);

//api post insert into database
Route::post('/oprform', [ApiInsertController::class, 'InsertOPRForm']);
Route::post('/oprform/rf', [ApiInsertController::class, 'InsertOPRFormRF']);
Route::post('/cpinsert', [ApiInsertController::class, 'InsertTechCPForm']);
Route::post('/rfinsert', [ApiInsertController::class, 'InsertTechRFForm']);
Route::post('/insert/master', [ApiInsertController::class, 'MasterApprove']);
Route::post('/insert/approve', [ApiInsertController::class, 'InsertAppr']);

//api put and delete
Route::put('/oprform/update/{id}', [ApiUpdateAndDeleteController::class, 'UpdateOprForm']);
Route::put('/oprform/rf/update/{id}', [ApiUpdateAndDeleteController::class, 'UpdateOprRfForm']);
Route::delete('/data/delete/{id}', [ApiUpdateAndDeleteController::class, 'DeleteDataOprForm']);
Route::put('/submit/{id}', [ApiUpdateAndDeleteController::class, 'SubmitDataOprForm']);
