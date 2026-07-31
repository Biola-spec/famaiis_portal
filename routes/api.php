<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    
    // Student Dashboard Data
    Route::get('/student/dashboard', [StudentController::class, 'dashboard']);
});

Route::get('/website-settings', function () {
    return \App\Models\WebsiteSetting::pluck('value', 'key');
});

Route::get('/school-settings', function () {
    $setting = \App\Models\SiteSetting::find(1);
    if ($setting) {
        if ($setting->logo) {
            $setting->logo_url = url($setting->logo);
        } else {
            $setting->logo_url = url('upload/logo/no_image.jpg');
        }
    }
    return response()->json($setting);
});

