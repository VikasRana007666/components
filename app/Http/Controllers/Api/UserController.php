<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use Illuminate\Http\Response;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'email' => 'required|email|string|unique:users',
            'password' => 'required|confirmed|min:8'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        event(new Registered($user));

        // $token = $user->createToken('user-token')->plainTextToken;

        $response = [
            'message' => 'User Created succesfully!'
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first() ?? "na";

        if ($user == "na") {
            return response()->json([
                "error" => "Invalid User"
            ]);
        } else {
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'error' => 'Bad Credentials'
                ]);
            }
        }

        $token = $user->createToken('user-token')->plainTextToken;

        return response()->json([
            'User Profile' => $user,
            'Token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();

        $user->tokens()->delete();

        $response = [
            'message' => 'User Logout Successfully!'
        ];

        return response()->json($response);
    }

    public function __invokenoti(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(RouteServiceProvider::HOME)
            : view('auth.verify-email');
    }

    public function __invokeveri(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
    }

    public function verinotistore(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
