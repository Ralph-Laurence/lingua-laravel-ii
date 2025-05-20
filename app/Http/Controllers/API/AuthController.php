<?php

namespace App\Http\Controllers\API;

use App\ApiServices\ResponseCode;
use App\Http\Controllers\Controller;
use App\Models\FieldNames\UserFields;
use App\Models\User;
use App\Services\LearnerSvc;
use App\Services\TutorSvc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'umail'     => 'required|string',   // accept both email and username,
            'password'  => 'required|string'
        ];

        $messages = [
            'umail.required'    => 'Please enter your username or email.',
            'umail.string'      => 'One of your entries is not allowed.',
            'password.required' => 'Please enter your password.',
            'password.string'   => 'One of your entries is not allowed.',
        ];

        $validator = Validator::make($request->only(array_keys($rules)), $rules, $messages);

        if ($validator->fails())
        {
            return response()->json($validator->errors(), ResponseCode::VALIDATION_ERROR);
        }

        $credentials = [
            filter_var($request->umail, FILTER_VALIDATE_EMAIL) ? 'email' : 'username' => $request->umail,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials))
        {
            $user  = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;
            $data  = ['message' => 'Logged In', 'token' => $token, 'user' => $user];

            if ($user[UserFields::Role] == User::ROLE_LEARNER)
                $data['user']['hashedId'] = LearnerSvc::toHashedId($user['id']);

            else if ($user[UserFields::Role] == User::ROLE_TUTOR)
                $data['user']['hashedId'] = TutorSvc::toHashedId($user['id']);

            return response()->json($data, ResponseCode::OK);
        }

        return response()->json(
            ['message' => 'Oops! It looks like the username, email, or password you entered is incorrect. Please try again.'],
            ResponseCode::UNAUTHORIZED
        );
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => "You've logged out successfully. Come back anytime!"], ResponseCode::OK);
    }
}
