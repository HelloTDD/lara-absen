<?php 
use Illuminate\Support\Facades\Log as lgs;
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
    
    lgs::info($data);
    return redirect()->back()->with($stats, $message);
}