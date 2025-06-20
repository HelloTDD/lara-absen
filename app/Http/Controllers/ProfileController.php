<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Log;
use App\Models\UserSalary;
use Illuminate\Http\Request;
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
        $data = UserSalary::where('user_id', Auth::user()->id)->where('month',now()->format('m'))->where('year', now()->format('Y'))->first();
        $monthlist = monthList();
        $yearlist = yearList();
        return view('user.profile.index', compact('data','monthlist', 'yearlist'));
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
            $data = UserSalary::with('user')->where('user_id',Auth::user()->id)->where('month',$request->month)->where('year',$request->year)->first();
            if (!$data) {
                return redirect()->route('profile.index')->with('error', 'Sallary not found.');
            }
            $pdf = new \Dompdf\Dompdf();
            $pdf->loadHtml(view('user.pdf.salary-slip', compact('data')));
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
