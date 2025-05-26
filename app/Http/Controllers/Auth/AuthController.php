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
        return view('welcome');
    }

    public function login(AuthRequest $req)
    {
        $credentials = $req->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->to('/homes');
        } else {
            return redirect()->route('home');
        }
    }

    public function logout()
    {
        Auth::logout();
        return view('welcome');
    }
}