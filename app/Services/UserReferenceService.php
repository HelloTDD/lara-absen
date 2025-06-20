<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Log as lg;
use App\Models\UserContract;
use Illuminate\Http\Request;
use App\Models\UserReference;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserReferenceService
{
    public function createReference($request)
    {
        $contract = UserContract::with('user')->where('user_id', $request->user_id ?? auth()->id())
            ->first();
        if (!$contract) {
            return redirect()->route('user-references.index')->with('error', 'No active contract found for the user.');
        }

        // dd($request->all(), $contract->toArray());
        DB::beginTransaction();
        try {
            $userReference = new UserReference();
            $userReference->user_id =  $request->user_id ?? auth()->id();
            $userReference->contract_id = $contract->id;
            $userReference->references_no = '204/'.Carbon::now()->format('d').'/'.Carbon::now()->format('m').'/'.Carbon::now()->format('Y');
            $userReference->name = $contract->user->name;
            $userReference->desc_references = $request->desc_references;
            $userReference->approve_with = Auth()->user()->name;
            $userReference->status_references = 'PENDING';
            $userReference->references_date = $request->references_date;

            if($userReference->save()){
                $return = true;
            } else {
                throw new \Exception("Gagal Menyimpan data", 1);
            }
            DB::commit();
            Log::info('tambah data berhasil', $userReference->toArray());

        } catch (\Throwable $th) {
            DB::rollBack();
            lg::create([
                'action' => 'create user reference',
                'controller' => 'UserReferenceController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);

            $return = false;
        }

        return $return;

    }
}
