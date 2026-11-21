<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user)],
            'mobile_no' => ['required', 'string', Rule::unique('users')->ignore($this->user)],
            'password' => 'nullable|confirmed|min:8', // Nullable on update
            'admin_type' => 'required|string',
            'applicant_image' => 'nullable|image|max:2048',
        ];
    }
}
