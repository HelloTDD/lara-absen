<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Interfaces\AuthInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller implements AuthInterface
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(AuthRequest $req)
    {
        $credentials = $req->only('username', 'password');

        if (Auth::attempt($credentials)) {
            // response JSON untuk success
            return response()->json([
                'success' => true,
                'redirect' => url('/homes')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid username or password.'
            ], 401);
        }
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
