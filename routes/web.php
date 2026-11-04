<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiGetDataController;

Route::middleware(['web'])->group(function () {
    Route::get('/session/user', [ApiGetDataController::class, 'GetSessionUser']);
});


Route::get('/', function () {
    return view('welcome');
});
