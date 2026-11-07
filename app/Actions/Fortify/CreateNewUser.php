<?php

namespace App\Actions\Fortify;

use App\enums\UserRole;
use App\enums\UserStatus;
use App\Models\User;
use App\Rules\NigerianPhone;
use App\Support\UserIdentifier;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $input = array_merge($input, UserIdentifier::forValidation($input['email'] ?? null, $input['phone'] ?? null));

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable', 'required_without:phone',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'phone' => ['nullable', 'required_without:email', 'string', new NigerianPhone, 'max:20', Rule::unique(User::class, 'phone')],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'password' => Hash::make($input['password']),
            'role' => UserRole::MEMBER,
            'status' => UserStatus::ACTIVE,
            'can_login' => true,
            'email_verified_at' => now(),
        ]);
    }
}
