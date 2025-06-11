<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Log as lgs;
class UserSalaryRequest extends FormRequest
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
            'user_id'=>'required|exists:users,id',
            'salary_basic'=>'required|numeric',
            'salary_allowance'=>'required|array',
            'salary_bonus'=>'required|numeric',
            'salary_holiday'=>'required|numeric',
        ];
    }

    // public function messages(): array
    // {
    //     return [
    //         'user_id.required' => 'User ID is required',
    //         'user_id.exists' => 'User ID does not exist',
    //         'salary_basic.required' => 'Basic salary is required',
    //         'salary_basic.numeric' => 'Basic salary must be a number',
    //         'salary_allowance.required' => 'Allowance is required',
    //         'salary_bonus.required' => 'Bonus is required',
    //         'salary_bonus.numeric' => 'Bonus must be a number',
    //         'salary_holiday.required' => 'Holiday salary is required',
    //         'salary_holiday.numeric' => 'Holiday salary must be a number',
    //     ];
    // }
}
