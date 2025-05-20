<?php

namespace App\Exceptions;

use App\ApiServices\ResponseCode;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * This override is necessary to change the behavior of the login page.
     * It ensures that API requests expecting JSON responses receive the
     * appropriate response while web requests continue to behave as expected.
     * In other words, API access will receive a JSON response, whereas web access
     * will be redirected to the login page.
     */
    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthenticated.'], status: ResponseCode::UNAUTHORIZED);
        }

        return redirect()->guest(route('login'));
    }
}
