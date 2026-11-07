<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\NigerianPhone;
use App\Support\UserIdentifier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(UserIdentifier::forValidation($this->input('email'), $this->input('phone')));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable', 'required_without:phone',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => [
                'nullable', 'required_without:email', 'string', new NigerianPhone, 'max:20',
                Rule::unique(User::class, 'phone')->ignore($this->user()->id),
            ],
        ];
    }
}
