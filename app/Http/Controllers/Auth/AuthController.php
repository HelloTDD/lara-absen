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

            return redirect()->to('/homes');
        } else {
            return redirect()->back()->with('error','Invalid username or password.')->withInput($req->only('email'));
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
