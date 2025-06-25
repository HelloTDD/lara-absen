<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Log;
use App\Models\UserBank;
use App\Models\UserSalary;
use Illuminate\Http\Request;
use App\Models\DetailAllowanceUser;
use App\Interfaces\ProfileInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller implements ProfileInterface
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = UserSalary::where('user_id', Auth::user()->id)->first();
        $userBank = UserBank::where('user_id', Auth::user()->id)->first();
        $monthlist = monthList();
        $yearlist = yearList();
        return view('user.profile.index', compact('data','monthlist', 'yearlist', 'userBank'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileRequest $request)
    {
        $result = null;
        try {
            $user = Auth::user();
            $result = $user->update([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender
            ]);
        } catch (\Exception $th) {
            Log::create([
                'action' => 'update profile',
                'controller' => 'ProfileController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($result);
    }


    /**
     * update bank
     */
    public function updateBank(Request $request)
    {
        $result = null;
        try {
            $user = Auth::user();
            $userBank = UserBank::where('user_id', $user->id)->first();
            //update or create user bank
            $result = UserBank::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'account_name' => $request->account_name,
                ]
            );

        } catch (\Exception $th) {
            Log::create([
                'action' => 'update bank',
                'controller' => 'ProfileController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($result);
    }


    /**
     * Update user password
     */
    public function changePassword(ProfileRequest $request)
    {
        $result = null;
        try {
            $user = Auth::user();
            if ($request->password === $request->confirm_password) {
                if (Hash::check($request->current_password, $user->password)) {
                    $result = $user->update([
                        'password' => Hash::make($request->password)
                    ]);
                } else {
                    $result = false;
                }
            } else {
                $result = false;
            }
        } catch (\Exception $th) {
            Log::create([
                'action' => 'change password',
                'controller' => 'ProfileController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($result);
    }

    /**
     * Download salary slip PDF
     */
    public function downloadSalarySlip(Request $request)
    {
        try {

            $data = UserSalary::with(['user.role'])->where('user_id',Auth::user()->id)
                                                        ->when($request->id_salaries, function($query) use ($request){
                                                            $query->where('id',$request->id_salaries);
                                                        })
                                                        ->when($request->month && $request->year, function($query) use ($request){
                                                            $query->where('month',$request->month)->where('year',$request->year);
                                                        })->first();

            $detail_allowances = DetailAllowanceUser::with('typeAllowance')->where('user_id',Auth::id())->get();

            if (!$data) {
                return redirect()->route('profile.index')->with('error', 'Sallary not found.');
            }

            $pdf = new \Dompdf\Dompdf();
            $pdf->loadHtml(view('user.pdf.salary-slip', compact('data','detail_allowances')));
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();
            return $pdf->stream('salary-slip-' . Auth::user()->name . '.pdf');
        } catch (\Exception $th) {
            Log::create([
                'action' => 'download salary slip',
                'controller' => 'ProfileController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
            return response()->json(['error' => 'Failed to download salary slip'], 500);
        }
    }
}
