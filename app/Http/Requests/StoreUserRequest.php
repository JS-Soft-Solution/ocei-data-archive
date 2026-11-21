<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized via middleware
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'mobile_no' => 'required|string|max:18|unique:users,mobile_no',
            'password' => ['required', 'confirmed', Password::defaults()],
            'admin_type' => 'required|string',
            'nid_no' => 'nullable|numeric',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string',
            // Address fields
            'per_district' => 'nullable|string',
            'pre_district' => 'nullable|string',
            // Images
            'applicant_image' => 'nullable|image|max:2048', // 2MB Max
        ];
    }
}
