@props(['user', 'roles', 'departments', 'states'])

<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Edit User</div>
                        <a href="{{ route('dashboard.users.index') }}" class="btn btn-light">Back</a>
                    </div>

                    <div class="card-body">

                        <!-- Validation Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('dashboard.users.update', $user->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">

                                <!-- Current Avatar Preview -->
                                <div class="col-md-12 mb-3 text-center">
                                    <img src="{{ $user->avatar ?? asset('assets/img/default-avatar.png') }}"
                                        width="120" class="rounded-circle mb-2">
                                </div>

                                <!-- Upload New Avatar -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Upload New Avatar</label>
                                    <input type="file" name="avatar" class="form-control">
                                    <small class="text-muted">Optional: Upload a new avatar image</small>
                                </div>

                                <!-- Name -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                        class="form-control" required>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                        class="form-control" required>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                        class="form-control">
                                </div>

                                <!-- Address -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" value="{{ old('address', $user->address) }}"
                                        class="form-control">
                                </div>

                                <!-- Role -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Role</label>
                                    <select name="role" class="form-select" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->value }}"
                                                {{ $user->role->value == $role->value ? 'selected' : '' }}>
                                                {{ ucfirst($role->value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active"
                                            {{ $user->status->value == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="created"
                                            {{ $user->status->value == 'created' ? 'selected' : '' }}>Created</option>
                                        <option value="suspended"
                                            {{ $user->status->value == 'suspended' ? 'selected' : '' }}>Suspended
                                        </option>
                                    </select>
                                </div>

                                <!-- Departments -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Departments</label>
                                    <select name="departments[]" class="form-select">
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ $user->departments->contains($department->id) ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Day Joined -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Day Joined</label>
                                    <input type="date" name="day_joined"
                                        value="{{ old('day_joined', optional($user->day_joined)->format('Y-m-d')) }}"
                                        class="form-control">
                                </div>

                                <!-- Birthday -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Birthday</label>
                                    <input type="date" name="birthday"
                                        value="{{ old('birthday', optional($user->birthday)->format('Y-m-d')) }}"
                                        class="form-control">
                                </div>

                                <!-- Occupation -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Occupation</label>
                                    <input type="text" name="occupation"
                                        value="{{ old('occupation', $user->occupation) }}" class="form-control">
                                </div>

                                <!-- State of Origin -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">State of Origin</label>
                                    <select name="state_of_origin" class="form-select">
                                        @foreach ($states as $state)
                                            <option value="{{ $state }}"
                                                {{ $user->state_of_origin == $state ? 'selected' : '' }}>
                                                {{ $state }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- What Attracted You -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">What Attracted You</label>
                                    <textarea name="what_attracted_you" class="form-control">{{ old('what_attracted_you', $user->what_attracted_you) }}</textarea>
                                </div>


                                <!-- Hobbies -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hobbies</label>
                                    <textarea name="hobbies" class="form-control">{{ old('hobbies', $user->hobbies) }}</textarea>
                                </div>

                                <!-- Favourite Quote -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Favourite Quote</label>
                                    <textarea name="favourite_quote" class="form-control">{{ old('favourite_quote', $user->favourite_quote) }}</textarea>
                                </div>

                                <!-- Can Login -->
                                <div class="col-md-12 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="can_login" value="1" {{ old('can_login', $user->can_login) ? 'checked' : '' }}>
                                        <label class="form-check-label">Can Login</label>
                                    </div>
                                </div>

                            </div>

                            <hr>

                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary">Update User</button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>