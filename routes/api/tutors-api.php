<?php

use App\ApiServices\ResponseCode;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\LearnersApiController;
use App\Http\Controllers\API\TutorApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TuringTest;

/*
|--------------------------------------------------------------------------
| Unauthenticated API Routes
|--------------------------------------------------------------------------
|
| These routes can be accessed by guests
|
*/
// Route::get('androidtest', function() {
//     return response()->json(['message' => 'Hello From 192.168.1.178'], 200);
// });
// tutor hash
// 87kx8eV4Yq
// learner hash
// 23jZg5wRbn

Route::prefix('/signlingua/tutors')->controller(TutorApiController::class)->group(function()
{
    // Retrieve the full list of tutors
    Route::get('/test', 'index')->name('api.tutors.index.test');

    // Retrieve the tutor's details
    Route::get('/test/{id}', 'show')->name('api.tutors.show.test');

    // Get the tutors tied to the learner
    //Route::get('/tutors')->name('api.getConnectedTutors');

    // These routes require authenticated users
    Route::middleware('auth:sanctum')->group(function()
    {
        // Retrieve the full list of tutors
        Route::get('/', 'index')->name('api.tutors.index');

        // Retrieve the tutor's details
        Route::get('/{id}', 'show')->name('api.tutors.show');

        // Tutor hiring
        Route::post('/hire', 'hireTutor')->name('api.tutors.hire');
        Route::post('/hire/cancel', 'cancelHireTutor')->name('api.tutors.hire.cancel');
        Route::post('/hire/leave', 'leaveTutor')->name('api.tutors.hire.leave');
    });
});

Route::prefix('/signlingua/learners')->controller(LearnersApiController::class)
->group(function()
{
    // Retrieve the learner's tutors
    Route::get('/{id}/tutors/test', 'index');

    // These routes require authenticated users
    Route::middleware('auth:sanctum')->group(function()
    {
        // Retrieve the tutor's details
        Route::get('/{id}/tutors', 'index');//->name('api.tutors.show');
    });
});