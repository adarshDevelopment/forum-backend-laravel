<?php

// use App\Http\Controllers\Controller;

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Hash;

class AuthController extends RootController
{

    public function getUser()
    {
        // return 'inside getUser laravel';
        $user = request()->user();
        if (!$user) {
            return $this->sendError('Error fetching user');
        }
        return $this->sendSuccess('User successfully fetched', 'user', items: $user);
    }
    public function register(Request $request)
    {
        // validate requests
        $validatedFields = $request->validate([
            'email' => 'email|unique:users',
            'name' => 'required|unique:users|string|max:255|min:8',
            'password' => 'required|confirmed|min:6|string|max:20'
        ]);


        // save on database and save on instance on $user

        // $user = DB::table('users')->insert($request->all());
        // $user = User::create($validatedFields);
        $user = User::create([
            'email' => $validatedFields['email'],
            'name' => $validatedFields['name'],
            'password' => Hash::make($validatedFields['password'])
        ]);
        if (!$user) {
            return $this->sendError('Error Creating user.', 500);
            // return response()->json(['message' => 'Error creating user', 'status' => false]);
        }

        dispatch(function () use ($user) {
            $user->sendEmailVerificationNotification();
        });

        // create and send token
        $token = $user->createToken($user->name);
        return $this->sendSuccess('User successfully registered from extension.', 'token', $token->plainTextToken);
        // return response()->json(['token' => $token->plainTextToken], 200);
    }

    public function resendEmailVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->sendSuccess('The email is already verifeid');
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->sendSuccess('Email verification successfully sent');
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        // hash vlaue contains the hashed email sent in the email content
        $user = User::find($id);
        if (!$user) {
            return $this->sendError('No User found', statusCode: 400);
        }
        if ($user->hasVerifiedEmail()) {
            return $this->sendSuccess('The email is already verifeid');
        }

        if (!hash_equals((string) $hash, sha1($user->email))) {
            return $this->sendError('Invalid or expired verification link', statusCode: 403);
        }
        $user->markEmailAsVerified();
        // or this
        // $user->email_verified_at = Carbon::now();
        return $this->sendSuccess(statusMessage: 'Email successfuilly verified');
    }


    public function login(Request $request)
    {
        // return $request;
        // validate request
        $validatedFields = $request->validate([
            'email' => 'required|email',
        ]);

        // check if user exists, if not or hash values dont match, send invalid credentials
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Invalid credentials', 401);
            // return response()->json(['status' => 'failed', 'message' => 'Invalid credentials'], 400);
        }

        // create and send token
        $token = $user->createToken($user->name);
        return $this->sendSuccess('User successfully logged in.', 'token', $token->plainTextToken);
        // return response()->json(['token' => $token->plainTextToken], 200);
    }

    public function logout(Request $request)
    {
        // return $request->user() ? 'user found' : 'user not found'; 
        if ($request->user()?->tokens()->delete()) {
            return $this->sendSuccess('User successfully logged out');
        } else {
            return $this->sendError('Error logging out User', 500);
        }
    }
}
