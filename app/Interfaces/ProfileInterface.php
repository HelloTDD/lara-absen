<?php

namespace App\Interfaces;
use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;

interface ProfileInterface
{
    public function index();

    public function update(ProfileRequest $request);

    public function changePassword(ProfileRequest $request);

    public function downloadSalarySlip(Request $request);
}
