<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserContract;
use Illuminate\Http\Request;
use App\Models\UserReference;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\UserReferenceService;
use App\Http\Requests\UserReferenceRequest;

class UserReferenceController extends Controller
{
    public function index()
    {
        $userReferences = UserReference::with(['user', 'userContract'])
            ->get();
        $users = User::all();
        return view('user.user-references.index', compact('userReferences', 'users'));
    }

    public function store(UserReferenceRequest $request, UserReferenceService $userReferenceService)
    {
        $contract = UserContract::with('user')->where('user_id', $request->user_id ?? auth()->id())
            ->first();
        if (!$contract) {
            return redirect()->route('user-references.index')->with('error', 'No active contract found for the user.');
        }
        DB::beginTransaction();
        try {
            $userReferenceService->createReference($request, $userReferenceService);

            // Update the contract's end date if it is null
            if (is_null($contract->end_contract_date)) {
                $contract->end_contract_date = Carbon::now()->addYear();
                $contract->save();
            }
        DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add user reference', [
                'error' => $e->getMessage(),
                'user_id' => $request->user_id,
                'contract_id' => $contract->id,
            ]);
            return redirect()->route('user-references.index')->with('error', 'Failed to add reference: ' . $e->getMessage());
        }

        return redirect()->route('user-references.index')->with('success', 'Reference added successfully.');
    }

    public function download($id)
    {
        try {
            $data = UserReference::with(['user', 'userContract'])
            ->first();
            // dd($contract);
            $pdf = new \Dompdf\Dompdf();
            $pdf->loadHtml(view('user.user-references.references', compact('data')));
            $pdf->setPaper('F4', 'portrait');
            $pdf->render();
            return $pdf->stream('reference-' . $data->name . '.pdf');
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'download user reference',
                'controller' => 'UserreferenceController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
            return back()->with('error', 'Failed to download reference');
        }
    }
}
