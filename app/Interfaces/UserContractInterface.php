<?php

namespace App\Interfaces;

use App\Http\Requests\UserContractRequest;
use App\Models\UserContract;
use App\Models\StatusContract;

interface UserContractInterface
{
    public function index();

    public function store(UserContractRequest $req,UserContract $userContract);
    public function status_update($status,$id,StatusContract $statusContract);

    public function update(UserContractRequest $req,UserContract $userContract,$id);

    public function delete($id,UserContract $userContract);

}
