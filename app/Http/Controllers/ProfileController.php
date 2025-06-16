<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\UserSalary;
use App\Interfaces\ProfileInterface;

class ProfileController extends Controller implements ProfileInterface
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = UserSalary::where('user_id', Auth::user()->id)->first();
        return view('user.profile.index', compact('data'));
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
    public function downloadSalarySlip()
    {
        try {
            $data = UserSalary::with('user')->where('user_id',Auth::user()->id)->first();
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
