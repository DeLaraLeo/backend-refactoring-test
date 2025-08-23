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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($this->user)],
            'password' => ['sometimes', 'required', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.max' => 'Name cannot exceed 255 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
        ];
    }
}
