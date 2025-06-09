<?php

namespace App\Interfaces;

use App\Http\Requests\UserLeaveRequest;
interface UserLeaveInterface
{
    public function index();
    public function index_by_user();
    public function create_leave(UserLeaveRequest $request);
    public function update_leave(UserLeaveRequest $request, $id);
    public function delete_leave($id);
    public function approve_leave($id);
    public function reject_leave($id);
}
