<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as lgs;

class ConfigController extends Controller
{
    public function index()
    {
        $configs = Config::first();
        return view('config.index', compact('configs'));
    }

    public function update(Request $request)
    {
        $result = null;
        try {
            $configs = Config::first();
            if (!$configs) {
                $result = Config::Create(
                [
                    'key' => $request->key,
                    'value' => $request->value,
                    'attendance_count' => $request->attendance_count,
                    'description' => $request->description,
                ]
            );
            }else{
                $result = Config::updateOrCreate(
                    ['id' => $configs->id],
                    [
                        'key' => $request->key,
                        'value' => $request->value,
                        'attendance_count' => $request->attendance_count,
                        'description' => $request->description,
                    ]
                );
            }

        } catch (\Exception $th) {
            Log::create([
                'action' => 'update configuration',
                'controller' => 'ConfigurationController',
                'error_code' => $th->getCode(),
                'description' => $th->getMessage(),
            ]);
        }

        return returnProccessData($result);
    }
}
