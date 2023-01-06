<?php

namespace Modules\Base\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AuthenticationController extends Controller
{

    //this method adds new users
    public function register(Request $request)
    {
        $attr = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]
        );

        $user = User::create(
            [
                'name' => $attr['name'],
                'password' => \bcrypt($attr['password']),
                'email' => $attr['email'],
            ]
        );

        return Response::json([
            'status' => true,
            'user' => $user,
            'token_type' => 'Bearer',
            'token' => $user->createToken('tokens')->plainTextToken,
        ]);
    }
    //use this method to signin users
    public function login(Request $request)
    {
        $attr = $request->validate(
            [
                'email' => 'required|string|email|',
                'password' => 'required|string|min:6',
            ]
        );

        if (!Auth::attempt($attr)) {
            return Response::json([
                'status' => false,
                'message' => 'Credentials not match',
            ]);
        }

        return Response::json([
            'status' => true,
            'user' => auth()->user(),
            'message' => 'Hi ' . auth()->user()->name . ', welcome to home',
            'token' => auth()->user()->createToken('API Token')->plainTextToken,
        ]);
    }
    //use this method to signin users
    public function autologin(Request $request)
    {

        $user = User::where('email', '=', $email)->first();

        if (Auth::id() != $user->id) {
            auth()->user()->tokens()->delete();
        }

        Auth::login($user, true);

        return Response::json([
            'status' => true,
            'user' => auth()->user(),
            'message' => 'Hi ' . auth()->user()->name . ', welcome to home',
            'token' => auth()->user()->createToken('API Token')->plainTextToken,
        ]);
    }

    // this method signs out users by removing tokens
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'status' => true,
            'message' => 'Tokens Revoked',
        ];
    }
}
