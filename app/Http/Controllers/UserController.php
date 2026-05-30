<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Constants\Constants;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->sort) {
            match ($request->sort) {
                'latest' => $query->latest(),
                'oldest' => $query->oldest(),
                'name' => $query->orderBy('name', 'asc'),
                'name_desc' => $query->orderBy('name', 'desc'),
            };
        } else {
            $query->latest();
        }

        $users = $query->paginate(10)->withQueryString();

        return view('dashboard.users.index', compact('users'));
    }

    /**
     * Show form to create user
     */
    public function create()
    {
        $roles = UserRole::cases();
        $departments = Department::all();
        $states = Constants::nigerianStates();


        return view('dashboard.users.create', compact('roles', 'departments', 'states'));
    }

    /**
     * Store new user (Admin creates user)
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'role'                  => ['required', 'in:' . implode(',', array_map(fn($case) => $case->value, UserRole::cases()))],
            'phone'                 => ['nullable', 'string'],
            'avatar'                => ['nullable', 'image', 'max:2048'], // optional, max 2MB
            'address'               => ['nullable', 'string', 'max:255'],
            'status'                => ['required', 'in:' . implode(',', array_map(fn($case) => $case->value, UserStatus::cases()))],
            'departments'           => ['nullable', 'array'],
            'departments.*'         => ['integer', 'exists:departments,id'],
            'day_joined'            => ['nullable', 'date'],
            'birthday'              => ['nullable', 'date'],
            'what_attracted_you'    => ['nullable', 'string'],
            'state_of_origin'       => ['nullable', 'string'],
            'occupation'            => ['nullable', 'string'],
            'hobbies'               => ['nullable', 'string'],
            'favourite_quote'       => ['nullable', 'string'],
            'can_login'             => ['required', 'boolean']
        ]);

        // Handle avatar upload if present
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // Generate default password
        $defaultPassword = Str::random(10);

        // Create user
        $user = User::create([
            'name'                  => $request->name,
            'email'                 => $request->email,
            'role'                  => $request->role,
            'phone'                 => $request->phone,
            'avatar'                => $avatarPath,
            'address'               => $request->address,
            'status'                => $request->status,
            'day_joined'            => $request->day_joined,
            'birthday'              => $request->birthday,
            'what_attracted_you'    => $request->what_attracted_you,
            'state_of_origin'       => $request->state_of_origin,
            'occupation'            => $request->occupation,
            'hobbies'               => $request->hobbies,
            'favourite_quote'       => $request->favourite_quote,
            'password'              => Hash::make($defaultPassword),
            'must_change_password'  => true,
            'can_login'             => $request->can_login,
        ]);

        // Sync departments if provided
        if ($request->departments) {
            $user->departments()->sync($request->departments);
        }

        $message = $request->can_login
            ? "User created successfully. Default password: $defaultPassword"
            : "User created successfully.";

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', $message);
    }

    /**
     * Show single user
     */
    public function show(User $user)
    {
        $user->load('departments');

        return view('dashboard.users.show', compact('user'));
    }

    /**
     * Edit user
     */
    public function edit(User $user)
    {
        $roles = UserRole::cases();
        $departments = Department::all();
        $states = Constants::nigerianStates();

        return view('dashboard.users.edit', compact('user', 'roles', 'departments', 'states'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email,' . $user->id],
            'role'                  => ['required', 'in:' . implode(',', array_map(fn($case) => $case->value, UserRole::cases()))],
            'phone'                 => ['nullable', 'string'],
            'avatar'                => ['nullable', 'image', 'max:2048'],
            'address'               => ['nullable', 'string', 'max:255'],
            'status'                => ['required', 'in:' . implode(',', array_map(fn($case) => $case->value, UserStatus::cases()))],
            'departments'           => ['nullable', 'array'],
            'departments.*'         => ['integer', 'exists:departments,id'],
            'day_joined'            => ['nullable', 'date'],
            'birthday'              => ['nullable', 'date'],
            'what_attracted_you'    => ['nullable', 'string'],
            'state_of_origin'       => ['nullable', 'string'],
            'occupation'            => ['nullable', 'string'],
            'hobbies'               => ['nullable', 'string'],
            'favourite_quote'       => ['nullable', 'string'],
            'can_login'             => ['required', 'boolean']
        ]);

        $data = $request->except(['avatar', 'departments']);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if it exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        if ($request->departments) {
            $user->departments()->sync($request->departments);
        } else {
            $user->departments()->detach();
        }

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Reset user password
     */
    public function resetPassword(User $user)
    {
        $newPassword = Str::random(10);
        $user->password = Hash::make($newPassword);
        $user->must_change_password = true;
        $user->save();

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', "Password for $user->name has been reset. New password: $newPassword");
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()
                ->route('dashboard.users.index')
                ->with('error', 'Admin users cannot be deleted');
        }
        $user->delete();

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'User deleted successfully');
    }

    /**
     * Manage user departments
     */
    public function departments(User $user)
    {
        $departments = Department::all();

        return view('dashboard.users.departments', compact('user', 'departments'));
    }

    /**
     * Update user departments
     */
    public function updateDepartments(Request $request, User $user)
    {
        $user->departments()->sync($request->departments);

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'Departments updated successfully');
    }

    /**
     * Send message to user (placeholder)
     */
    public function message(User $user)
    {
        return view('dashboard.users.message', compact('user'));
    }
}
