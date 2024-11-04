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

    public function register(Request $request)
    {
        // validate requests
        $validatedFields = $request->validate([
            'email' => 'email|unique:users',
            'name' => 'required|string|max:255',
            'password' => 'required|confirmed|min:6|string'

        ]);


        // save on database and save on instance on $user

        // $user = DB::table('users')->insert($request->all());
        $user = User::create([
            'email' => $validatedFields['email'],
            'name' => $validatedFields['name'],
            'password' => Hash::make($validatedFields['password'])
        ]);
        if (!$user) {
            return $this->sendError('Error Creating user.', 500);
            // return response()->json(['message' => 'Error creating user', 'status' => false]);
        }

        // create and send token
        $token = $user->createToken($user->name);
        return $this->sendSuccess('User successfully registered from extension.', 'token', $token->plainTextToken);
        // return response()->json(['token' => $token->plainTextToken], 200);
    }

    public function login(Request $request)
    {
        // validate request
        $validatedFields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|max:32'
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
        } {
            return $this->sendError('Error logging out User', 500);
        }
    }
}
