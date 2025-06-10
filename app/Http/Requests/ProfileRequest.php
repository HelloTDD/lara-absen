<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name' => request()->routeIs('profile.change.password') ? 'nullable' : 'required|string|max:255',
            'username' => request()->routeIs('profile.change.password') ? 'nullable' : 'required|string|max:255',
            'email' => request()->routeIs('profile.change.password') ? 'nullable' : 'required|email|max:255|unique:users,email,' . $this->user()->id,
            'current_password' => request()->routeIs('profile.change.password') ? 'required|string' : 'nullable',
            'password' => request()->routeIs('profile.change.password') ? 'required|string|min:8' : 'nullable',
            'confirm_password' => request()->routeIs('profile.change.password') ? 'required|string|min:8' : 'nullable',
            'phone' => request()->routeIs('profile.change.password') ? 'nullable' : 'required|string|max:15',
            'address' => request()->routeIs('profile.change.password') ? 'nullable' : 'required|string|max:255',
            'birth_date' => request()->routeIs('profile.change.password') ? 'nullable' : 'required|date',
            'gender' => request()->routeIs('profile.change.password') ? 'nullable' : 'required|in:Laki-laki,Perempuan'
        ];
    }
}
