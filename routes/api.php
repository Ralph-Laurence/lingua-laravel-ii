<?php

use App\ApiServices\ResponseCode;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TuringTest;
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

// Route::middleware('auth:api')->group(function()
// {

// });
Route::controller(TuringTest::class)->group(function() {

    Route::get('/recaptcha', 'generateNewCaptcha')->name('recaptcha');
    Route::post('/validate-captcha', [TuringTest::class, 'validateCaptcha']);
});

Route::controller(AuthController::class)->group(function()
{
    // Guest level routes
    Route::post('/login', 'login')->name('api.auth.login');

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function()
    {
        Route::post('/logout', 'logout')->name('api.auth.logout');
    });
});


// Tutor Routes
include 'api/test-api.php';
include 'api/tutors-api.php';
