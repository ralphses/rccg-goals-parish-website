<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\NigerianPhone;
use App\Support\UserIdentifier;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        $input = array_merge($input, UserIdentifier::forValidation($input['email'] ?? null, $input['phone'] ?? null));

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'nullable', 'required_without:phone',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['nullable', 'required_without:email', 'string', new NigerianPhone, 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
        ])->validateWithBag('updateProfileInformation');

        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
        ])->save();
    }
}
