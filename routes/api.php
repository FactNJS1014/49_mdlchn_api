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
Route::get('/approve/users', [ApiGetDataController::class, 'GetApproveUsers']);
Route::get('/appr/data/cp', [ApiGetDataController::class, 'GetDataApprCP']);
Route::get('/get/cus', [ApiGetDataController::class, 'GetDataCustomer']);
Route::get('/get/issueno', [ApiGetDataController::class, 'GetIssueNo']);
Route::get('/get/settings', [ApiGetDataController::class, 'GetSettings']);
Route::get('/get/alerts', [ApiGetDataController::class, 'GetNotification']);
Route::get('/get/reports', [ApiGetDataController::class, 'GetReports']);
Route::get('/get/filter', [ApiGetDataController::class, 'DataFilters']);
Route::get('/get/reports/nocomplete', [ApiGetDataController::class, 'GetReportsNoComplete']);
Route::get('/get/rejectedData', [ApiGetDataController::class, 'GetRejectedData']);
Route::get('/get/reports/byid', [ApiGetDataController::class, 'GetReportsById']);

//api post insert into database
Route::post('/oprform', [ApiInsertController::class, 'InsertOPRForm']);
Route::post('/oprform/rf', [ApiInsertController::class, 'InsertOPRFormRF']);
Route::post('/cpinsert', [ApiInsertController::class, 'InsertTechCPForm']);
Route::post('/rfinsert', [ApiInsertController::class, 'InsertTechRFForm']);
Route::post('/insert/master', [ApiInsertController::class, 'MasterApprove']);
Route::post('/insert/approve', [ApiInsertController::class, 'InsertAppr']);
Route::post('/insert/approve/rf', [ApiInsertController::class, 'InsertApprRF']);
Route::post('/insert/settings', [ApiInsertController::class, 'InsertSettings']);
Route::post('/rfinsert-test', [ApiInsertController::class, 'InsertTechRFFormTest']);

//TODO : IDC = IMPORT DOCUMENT ECN
Route::post('/IDC/ECN',[ApiInsertController::class, 'UploadDocumentECN']);


//api put and delete
Route::put('/oprform/update/{id}', [ApiUpdateAndDeleteController::class, 'UpdateOprForm']);
Route::put('/oprform/rf/update/{id}', [ApiUpdateAndDeleteController::class, 'UpdateOprRfForm']);
Route::delete('/data/delete/{id}', [ApiUpdateAndDeleteController::class, 'DeleteDataOprForm']);
Route::put('/submit/{id}', [ApiUpdateAndDeleteController::class, 'SubmitDataOprForm']);
Route::put('/update/approve', [ApiUpdateAndDeleteController::class, 'UpdateApproveStatus']);
Route::put('/update/reject', [ApiUpdateAndDeleteController::class, 'UpdateRejectStatus']);
Route::put('/cpupdate', [ApiUpdateAndDeleteController::class, 'UpdateCPData']);
Route::put('/rfupdate', [ApiUpdateAndDeleteController::class, 'UpdateRFData']);
Route::put('/update/master', [ApiUpdateAndDeleteController::class, 'UpdateMasterLevel']);
Route::put('/update/std/delete', [ApiUpdateAndDeleteController::class, 'DeleteStdData']);
Route::delete('/delete/techform/{id}', [ApiUpdateAndDeleteController::class, 'DeleteTechForm']);
