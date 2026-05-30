<div class="container">
    <div class="page-inner">

        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Create New User</div>
                        <a href="{{ route('dashboard.users.index') }}" class="btn btn-light">
                            Back
                        </a>
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

                        <form method="POST" action="{{ route('dashboard.users.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                <!-- Avatar Upload -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Upload Avatar</label>
                                    <input type="file" name="avatar" class="form-control">
                                    <small class="text-muted">Optional: Upload a profile picture</small>
                                </div>

                                <!-- Name -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                                </div>

                                <!-- Address -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" value="{{ old('address') }}" class="form-control">
                                </div>

                                <!-- Role -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Role</label>
                                    <select name="role" class="form-select" required>
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->value }}" {{ old('role') == $role->value ? 'selected' : '' }}>
                                                {{ ucfirst($role->value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active" {{ old('status')=='active' ? 'selected' : '' }}>Active</option>
                                        <option value="created" {{ old('status')=='created' ? 'selected' : '' }}>Created</option>
                                        <option value="suspended" {{ old('status')=='suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                </div>

                                  <!-- Status -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Can Login</label>
                                    <select name="can_login" class="form-select">
                                        <option value="0" {{ old('can_login')=='0' ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('can_login')=='1' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>

                                <!-- Departments -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Departments</label>
                                    <select name="departments[]" class="form-select">
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ in_array($department->id, old('departments', [])) ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Hold CTRL (Windows) or CMD (Mac) to select multiple departments</small>
                                </div>

                                <!-- Day Joined -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Day Joined</label>
                                    <input type="date" name="day_joined" value="{{ old('day_joined') }}" class="form-control">
                                </div>

                                <!-- Birthday -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Birthday</label>
                                    <input type="date" name="birthday" value="{{ old('birthday') }}" class="form-control">
                                </div>

                                 <!-- Occupation -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Occupation</label>
                                    <input type="text" name="occupation" value="{{ old('occupation') }}" class="form-control">
                                </div>


                                <!-- State of Origin -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">State of Origin</label>
                                    <select name="state_of_origin" class="form-select">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state }}" {{ old('state_of_origin')==$state ? 'selected' : '' }}>
                                                {{ $state }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Hobbies -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hobbies</label>
                                    <textarea name="hobbies" class="form-control">{{ old('hobbies') }}</textarea>
                                </div>

                                <!-- Favourite Quote -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Favourite Quote</label>
                                    <textarea name="favourite_quote" class="form-control">{{ old('favourite_quote') }}</textarea>
                                </div>

                            </div>



                                <!-- What Attracted You -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">What Attracted You</label>
                                    <textarea name="what_attracted_you" class="form-control">{{ old('what_attracted_you') }}</textarea>
                                </div>

                            <hr>

                            <!-- Submit -->
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary">Create User</button>
                            </div>

                        </form>

                        <div class="mt-3 text-muted">
                            Note: A <strong>default password will be automatically generated</strong> and the user will be required to change it on first login.
                        </div>

                    </div>

                </div>

            </div>
        </div>

    </div>
</div>