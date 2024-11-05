<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * log in GET
     */
    public function login_get()
    {
        return response()->json(["ok" => false, "status" => 401], 401); //return a 400 unauthorized if a user is not logged in
    }

    /**
     * log in POST
     */
    public function login_post(Request $request)
    {
        $credentials = $request->validate([ //validate user inputs
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) { //log in user if the credentials are correct
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken("patrick bremm token"); //create a token for the logged in user
            return response()->json([["ok" => true, 'user' => $user, 'token' => $token->plainTextToken], "status" => 200], 200);
        }

        return response()->json(["ok" => false, "status" => 400], 400);
    }

    /**
     * sign up in POST
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create a new user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $token = $user->createToken("patrick bremm token");

        return response()->json([["ok" => true, 'user' => $user, 'token' => $token->plainTextToken], "status" => 201], 201);
    }
}
