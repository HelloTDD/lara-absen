<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserEmployeeRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            // 'username' => 'required|string|max:255|unique:users,username,',
            // 'email' => 'required|email|max:255|unique:users,email,',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:30',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|in:Laki-laki,Perempuan',
            'address' => 'nullable|string|max:500',
            'leave' => 'nullable|integer|min:0',
            'date_joined'  => 'nullable|date',
            'date_leave' => 'nullable|date',
        ];
    }
}
