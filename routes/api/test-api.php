<?php

use App\ApiServices\ResponseCode;
use App\Http\Controllers\Api\AuthController;
use App\Services\LearnerSvc;
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

Route::get('androidtest', function() {
    return response()->json(['message' => 'Hello From 192.168.1.178'], 200);
});

Route::prefix('/signlingua/learner')->group(function()
{
    Route::get('/hashlearnerid/{id}', function($id)
    {
        $hashedid = LearnerSvc::toHashedId($id);

        return response()->json(['raw' => $id, 'hashed'=> $hashedid],200);
    });
});

Route::middleware('auth:sanctum')->group(function()
{
    Route::get('/onlyAuth', function() {
        return response()->json(['message'=> 'Access to only authenticated users!'], ResponseCode::UNAUTHORIZED);
    });
});