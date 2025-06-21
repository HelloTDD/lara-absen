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
       $contract = UserContract::with(['user', 'status_contract'])
        ->where('user_id', $request->user_id ?? auth()->id())
        ->whereHas('status_contract', function ($query) {
            $query->where('status_contract', 'APPROVE');
        })
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
            ->where('id', $id)
            ->first();
            // dd($contract);
            $pdf = new \Dompdf\Dompdf();
            $pdf->loadHtml(view('user.user-references.references', compact('data')));
            $pdf->setPaper([0, 0, 595.28, 935.43], 'portrait'); // Ukuran F4
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

    public function preview($id)
    {
        try {
            $data = UserReference::with(['user', 'userContract'])
                ->where('id', $id)
                ->first();

            $pdf = new \Dompdf\Dompdf();
            $pdf->loadHtml(view('user.user-references.references', compact('data')));
            $pdf->setPaper([0, 0, 595.28, 935.43], 'portrait');
            $pdf->render();

            // Stream tanpa attachment header agar tampil di tab baru
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf');
        } catch (\Throwable $th) {
            Log::error('Failed to preview user reference', [
                'action' => 'preview user reference',
                'controller' => 'UserReferenceController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
            return back()->with('error', 'Failed to preview reference');
        }
    }

    public function update(UserReferenceRequest $request, $id, UserReferenceService $userReferenceService)
    {
        $reference = UserReference::findOrFail($id);

        $contract = UserContract::with(['user', 'status_contract'])
            ->where('user_id', $request->user_id ?? auth()->id())
            ->whereHas('status_contract', function ($query) {
                $query->where('status_contract', 'APPROVE');
            })
            ->first();

        if (!$contract) {
            return redirect()->route('user-references.index')->with('error', 'No active contract found for the user.');
        }

        DB::beginTransaction();
        try {
            // Update field manual
            $reference->user_id = $request->user_id ?? auth()->id();
            $reference->references_date = $request->references_date;
            $reference->desc_references = $request->desc_references;
            $reference->save();

            // Optional: Update kontrak jika end_contract_date masih null
            if (is_null($contract->end_contract_date)) {
                $contract->end_contract_date = Carbon::now()->addYear();
                $contract->save();
            }

            DB::commit();
            return redirect()->route('user-references.index')->with('success', 'Reference updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user reference', [
                'error' => $e->getMessage(),
                'user_id' => $request->user_id,
                'reference_id' => $id,
            ]);
            return redirect()->route('user-references.index')->with('error', 'Failed to update reference: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $reference = UserReference::findOrFail($id);
            $reference->delete();

            DB::commit();
            return redirect()->route('user-references.index')->with('success', 'Reference deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user reference', [
                'error' => $e->getMessage(),
                'reference_id' => $id,
            ]);

            return redirect()->route('user-references.index')->with('error', 'Failed to delete reference: ' . $e->getMessage());
        }
    }



}
