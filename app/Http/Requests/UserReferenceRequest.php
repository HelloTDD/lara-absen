<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserReferenceRequest extends FormRequest
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
            // 'user_id' => request()->routeIs('user-references.update') ? '' : 'required|exists:users,id',
            'desc_references' => 'required|string|max:500',
            'approve_with' => 'nullable|string|max:255',
            'references_date' => 'required|date',
        ];
    }
}
