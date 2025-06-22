<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeAllowance;
use App\Models\Log;

class AllowanceController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $result = null;
        try {
            $result = TypeAllowance::create([
                'name_allowance' => $request->name,
            ]);
        } catch (\Exception $e) {
            Log::create([
                'action' => 'create type allowance',
                'controller' => 'AllowanceController',
                'error_code' => $e->getCode(),
                'description' => $e->getMessage(),
            ]);
        }
        return returnProccessData($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = null;
        try {
            $type_allowance = TypeAllowance::find($id);
            if ($type_allowance) {
                $result = $type_allowance->delete();
            } else {
                throw new \Exception('Type allowance not found');
            }
        } catch (\Exception $e) {
            Log::create([
                'action' => 'delete type allowance',
                'controller' => 'AllowanceController',
                'error_code' => $e->getCode(),
                'description' => $e->getMessage(),
            ]);
        }
        return returnProccessData($result);
    }
}
