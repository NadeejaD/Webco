<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/get-all-customers', [\App\Http\Controllers\CustomerController::class, 'all_customers']);
Route::post('/create-customer', [\App\Http\Controllers\CustomerController::class, 'store']);
Route::get('/show-customer/{id}', [\App\Http\Controllers\CustomerController::class, 'show']);
Route::put('/update-customer/{id}', [\App\Http\Controllers\CustomerController::class, 'update']);
Route::delete('/delete-customer/{id}', [\App\Http\Controllers\CustomerController::class, 'delete']);

Route::get('/get-all-projects', [\App\Http\Controllers\ProjectController::class, 'all_projects']);
Route::post('/create-project', [\App\Http\Controllers\ProjectController::class, 'store']);
Route::get('/show-project/{id}', [\App\Http\Controllers\ProjectController::class, 'show']);
Route::put('/update-project/{id}', [\App\Http\Controllers\ProjectController::class, 'update']);
Route::delete('/delete-project/{id}', [\App\Http\Controllers\ProjectController::class, 'delete']);
