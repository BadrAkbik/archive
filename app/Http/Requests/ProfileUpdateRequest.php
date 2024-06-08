<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique(User::class, 'username')->ignore($this->user()->id), 'regex:/^[a-zA-Z0-9]{6,14}$/'],
            'phone_num' => ['nullable', 'regex:/^[\+0-9]{8,15}$/'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => __('authpage.The username must be between 6 to 14 characters and contain only letters and numbers.'),
            'phone_num.regex' => __('authpage.Enter a correct Phone number.'),
        ];
    }
}
