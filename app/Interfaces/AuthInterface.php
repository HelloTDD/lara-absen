<?php

namespace App\Interfaces;

use App\Http\Requests\Auth\AuthRequest;
use App\models\User;
interface AuthInterface
{
    public function index();
    public function login(AuthRequest $request);
    // public function login(array $credentials);

    public function logout();

    // public function register(array $data);

    // public function resetPassword(array $data);

    // public function changePassword(array $data);
}
