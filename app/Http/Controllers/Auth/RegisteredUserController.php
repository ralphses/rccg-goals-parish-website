<?php

namespace App\Http\Controllers\Auth;

use App\enums\UserRole;
use App\enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\NigerianPhone;
use App\Support\UserIdentifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge(UserIdentifier::forValidation($request->input('email'), $request->input('phone')));

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'required_without:phone', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'required_without:email', 'string', new NigerianPhone, 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => UserRole::MEMBER,
            'status' => UserStatus::ACTIVE,
            'can_login' => true,
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
