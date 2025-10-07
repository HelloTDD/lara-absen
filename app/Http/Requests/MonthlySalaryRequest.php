<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonthlySalaryRequest extends FormRequest
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
        if ($this->isMethod('post')) {
            // saat create (store)
            return [
                'salary_ids'      => 'required|exists:user_salaries,id',
                'month'           => 'required|numeric',
                'year'            => 'required|numeric',
            ];
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // saat update
            return [
                'user_id'         => 'required|exists:users,id',
                'salary_basic'    => 'nullable|numeric|min:0',
                'salary_bonus'    => 'nullable|numeric|min:0',
                'salary_holiday'  => 'nullable|numeric|min:0',
                'allowances'      => 'nullable|array',
                'allowances.*'    => 'nullable|numeric|min:0',
            ];
        }

        return [];
    }

}
