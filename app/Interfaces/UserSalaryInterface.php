<?php

namespace App\Interfaces;

use App\Http\Requests\UserSalaryRequest;

interface UserSalaryInterface
{
    public function index();
    public function store(UserSalaryRequest $request);
}
