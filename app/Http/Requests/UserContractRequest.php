<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
class UserContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => request()->routeIs('user-contract.update') ? '' : 'required|exists:users,id',
            'desc_constract' => 'required',
            'start_contract_date' => 'required|date',
            'end_contract_date' => 'required|date|after_or_equal:start_contract_date',
            'file' => request()->routeIs('user-contract.update') ? '' : 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
        ];
    }
}
