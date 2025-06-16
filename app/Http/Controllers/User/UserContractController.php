<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserContractRequest;
use App\Models\UserContract;
use App\Interfaces\UserContractInterface;
use Exception;
use App\Models\Log;
use App\Models\StatusContract;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as lgs;
use Illuminate\Support\Str;

class UserContractController extends Controller implements UserContractInterface
{
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        $userContracts = StatusContract::with('contracts.user')->get();
        return view('user.users-contract.index', compact('userContracts','users'));
    }

    public function store(UserContractRequest $req,UserContract $userContract)
    {
        $result = null;
        DB::beginTransaction();
        try {
            $user = User::find($req->user_id);
            $file = $req->file('file');
            $fileName = trim($file->getClientOriginalName());
            $path = $file->storeAs('public/file',$fileName);

            if(!$path){
                throw new Exception("Gagal Mengupload File",1);
            }

            $files = 'storage/file/'.$fileName;

            $contract = $userContract->create([
                'user_id' => $user->id,
                'name' => $user->name,
                'desc_contract' => $req->desc_constract,
                'start_contract_date' => $req->start_contract_date,
                'end_contract_date' => $req->end_contract_date,
                'file' => $files,
            ]);

            if (!$contract) {
                throw new Exception("Failed to create contract", 1);
            }

            $result = StatusContract::create([
                'contract_id' => $contract->id
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::create([
                'action' => 'create user contract',
                'controller' => 'UserContractController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($result);
    }

    public function status_update ($status,$id,StatusContract $statusContract)
    {   
        $result = null;
        try {
            $result = $statusContract->where('id',$id)->update([
                'status_contract' => Str::upper($status)
            ]);
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'update status user contract',
                'controller' => 'UserContractController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($result);
    }

    public function update(UserContractRequest $req,UserContract $userContract,$id)
    {
        $result = null;
        try {
            $get_data = $userContract->find($id);
            if(!empty($req->file))
            {
                if(file_exists(public_path($get_data->file))){
                    unlink(public_path($get_data->file));
                }

                $file = $req->file('file');
                $fileName = trim($file->getClientOriginalName());
                $path = $file->storeAs('public/file',$fileName);

                if(!$path){
                    throw new Exception("Gagal Mengupload File",1);
                }

                $files = 'storage/file/'.$fileName;
            }

            $result = $get_data->update([
                'desc_contract' => $req->desc_constract,
                'start_contract_date' => $req->start_contract_date,
                'end_contract_date' => $req->end_contract_date,
                'file' => !empty($req->file) ? $files : $get_data->file
            ]);
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'update user contract',
                'controller' => 'UserContractController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($result);
    }

    public function delete($id,UserContract $userContract)
    {
        $result = null;
        DB::beginTransaction();
        try {
            $get_data = $userContract->find($id);
            if(file_exists(public_path($get_data->file))){
                unlink(public_path($get_data->file));
            }

            StatusContract::where('contract_id',$get_data->id)->delete();
            $result = $get_data->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::create([
                'action' => 'delete user contract',
                'controller' => 'UserContractController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($result);
    }
    public function download($id,StatusContract $statusContract)
    {
        try {
            $data = $statusContract->with(['contracts.user'])->where('id',$id)->first();
            // dd($contract);
            $pdf = new \Dompdf\Dompdf();
            $pdf->loadHtml(view('user.users-contract.contract', compact('data')));
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();
            return $pdf->stream('contract-' . $data->name . '.pdf');
        } catch (\Throwable $th) {
            Log::create([
                'action' => 'download user contract',
                'controller' => 'UserContractController',
                'error_code' => $th->getCode(), 
                'description' => $th->getMessage(),
            ]);
            return back()->with('error', 'Failed to download contract');
        }
    }
}
