<?php
use Illuminate\Support\Facades\Log as lgs;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Validation\Rules\Date as RulesDate;

function returnProccessData($data = null)
{
    $url = request()->url();
    if($data){
        $stats = 'success';
        if(str_contains($url, 'delete')){
            $message = 'Successfully deleted data';
        } elseif(str_contains($url, 'update')){
            $message = 'Successfully updated data';
        } else {
            $message = 'Successfully created data';
        }
    } else {
        $stats = 'error';

        if(str_contains($url, 'delete')){
            $message = 'Failed to delete data';
        } elseif(str_contains($url, 'update')){
            $message = 'Failed to update data';
        } else {
            $message = 'Failed to create data';
        }
    }

    // lgs::info($data);
    return redirect()->back()->with($stats, $message);
}

function dateTimeToday()
{
    return Carbon::now();
}
function timeNow()
{
    return Carbon::now()->format('H:i:s');
}

function todayNow()
{
    return Carbon::today()->format('Y-m-d');
}

function monthList() {
    return [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];
}

function yearlist() {
    $currentYear = Carbon::now()->year;
    $years = [];
    for ($i = $currentYear; $i >= 2000; $i--) {
        $years[$i] = $i;
    }
    return $years;
}

function filterSpecialChar($param)
{
    return str_replace(['"',"'"],' ',$param);
}