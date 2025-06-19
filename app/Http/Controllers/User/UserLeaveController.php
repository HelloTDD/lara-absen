<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\UserLeaveInterface;
use App\Http\Requests\UserLeaveRequest;
use App\Models\User;
use App\Models\UserLeave;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log as lgs;

class UserLeaveController extends Controller implements UserLeaveInterface
{
    public function index()
    {
        $users = User::all();
        $leaves = UserLeave::with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('user.users-leave.index', compact('users', 'leaves'));
    }

    public function index_by_user()
    {
        $user_id = Auth::user()->id;
        $leaves = UserLeave::where('user_id', $user_id)->with('user')->paginate(10);
        return view('user.users-leave.index', compact('leaves'));
    }

    public function create_leave(UserLeaveRequest $request)
    {
        $create_leave = null;
        try {
            $user_id = Auth::check() ? Auth::user()->id : User::where('id', $request->user_id)->first()->id;
            if(Auth::user()->is_admin == 1){
                $user = User::where('id', $request->user_id)->first();
            }else {
                $user = User::find($user_id);
            }

            if ($user) {
                $joinDate = Carbon::parse($user->date_joined);
                $oneYearAfterJoin = $joinDate->copy()->addYear();
                $today = Carbon::now();

                if ($today->lt($oneYearAfterJoin)) {
                    return redirect()->back()->with('error', 'User must be employed for at least 1 year to take leave');
                }
            }else {
                throw new \Exception("User tersebut tidak ada", 1);
            }
            if($user->leave <= 0 || $user->leave == '0'){
                return redirect()->back()->with('error', 'User has no leave');
            }

            $create_leave = UserLeave::create([
                'user_id' => $user->id,
                'leave_date_start' => $request->start_date,
                'leave_date_end' => $request->end_date,
                'desc_leave' => $request->description,
                'status' => 'pending',
            ]);
            if (!$create_leave) {
                throw new \Exception('Leave details not saved');
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'create user leave',
                'controller' => 'UserLeaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($create_leave);
    }
    public function update_leave(UserLeaveRequest $request, $id)
    {
        $leave = null;
        try {
            $leave = UserLeave::find($id);
            if ($leave) {
                $leave->update([
                    'leave_date_start' => $request->start_date,
                    'leave_date_end' => $request->end_date,
                    'desc_leave' => $request->description
                ]);
            } else {
                throw new \Exception('Leave not found');
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'update user leave',
                'controller' => 'UserLeaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($leave);
    }
    public function delete_leave($id)
    {
        $leave = null;
        try {
            $leave = UserLeave::find($id);
            if ($leave) {
                $leave->delete();
            } else {
                throw new \Exception('Leave not found');
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'delete user leave',
                'controller' => 'UserLeaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($leave);
    }
    public function approve_leave($id)
    {
        $leave = null;
        try {
            $leave = UserLeave::with('user')->find($id);

            if (!$leave) {
                throw new \Exception('Leave not found');
            }

            if($leave->user?->leave < 0 ){
                throw new \Exception('User has no leave');
            }

            if ($leave->user) {

                $joinDate = Carbon::parse($leave->user?->date_joined);
                $oneYearAfterJoin = $joinDate->copy()->addYear();
                $today = Carbon::now();

                if ($today->lt($oneYearAfterJoin)) {
                    throw new \Exception('User must be employed for at least 1 year to take leave');
                }

                $user = $leave->user;
                $user->leave = max(0, $user->leave - 1); // supaya tidak negatif
                $user->save();
            } else {
                throw new \Exception("User tersebut tidak ada", 1);
            }

            $leave->update([
                'status' => 'approved'
            ]);

        } catch (\Throwable $th) {

            Log::create([
                'action' => 'approve user leave',
                'controller' => 'UserLeaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);

        }

        return returnProccessData($leave);
    }
    public function reject_leave($id)
    {
        $leave = null;
        try {
            $leave = UserLeave::find($id);
            if ($leave) {
                $leave->update(['status' => 'rejected']);
            } else {
                throw new \Exception('Leave not found');
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'reject user leave',
                'controller' => 'UserLeaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($leave);
    }

    public function cancel_request($id)
    {
        $leave = null;
        try {
            $leave = UserLeave::find($id);
            if ($leave) {
                $leave->update([
                    'status' => 'cancel',
                ]);
            } else {
                throw new \Exception('Leave not found');
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'canceled user leave',
                'controller' => 'UserLeaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($leave);
    }

    public function cancel_leave($id)
    {
        $leave = null;
        try {
            $leave = UserLeave::with('user')->find($id);
            $user = User::where('id' , $leave->user_id)->first();
            if ($leave) {
                $leave->update([
                    'status' => 'rejected',
                ]);
                $user->update([
                    'leave' => $user->leave  += 1,
                ]);
            } else {
                throw new \Exception('Leave not found');
            }
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'pending user leave',
                'controller' => 'UserLeaveController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($leave);
    }
}
