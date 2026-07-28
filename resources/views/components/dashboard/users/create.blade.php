@include('components.dashboard.partials.form-shell')

<div class="container">
    <div class="page-inner">
        <div class="dashboard-form-shell">
            <div class="dashboard-form-hero card mb-4"><div class="card-body p-4 p-lg-5"><div class="row align-items-center g-4"><div class="col-lg-8"><span class="dashboard-form-eyebrow">Users Form</span><h2 class="dashboard-form-title">Create a user account with role, department, and profile setup in one place.</h2><p class="dashboard-form-subtitle">This workflow gives church admins a cleaner way to set permissions and member details before first login.</p></div><div class="col-lg-4"><div class="dashboard-form-hero-actions"><a href="{{ route('dashboard.users.index') }}" class="btn btn-outline-secondary btn-lg dashboard-form-secondary-btn">Back to Users</a><div class="dashboard-form-note"><span class="dot"></span>A default password will be generated automatically</div></div></div></div></div></div>
            <div class="card dashboard-form-card"><div class="card-header"><div class="card-title">Create New User</div></div><div class="card-body">
                @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ route('dashboard.users.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <x-dashboard.partials.cropped-image-field
                                label="Upload Avatar"
                                source-name="avatar_source"
                                cropped-name="avatar_cropped"
                                source-id="user-avatar-create"
                                helper="Optional: upload a profile picture, drag to frame the face well, then confirm the crop."
                                empty-state="Select an avatar image to begin cropping."
                                result-label="Final avatar"
                                :preview-rounded="true"
                            />
                        </div>
                        <div class="col-md-6 mb-3"><label class="form-label">Name</label><input type="text" name="name" value="{{ old('name') }}" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Phone</label><input type="text" name="phone" value="{{ old('phone') }}" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Address</label><input type="text" name="address" value="{{ old('address') }}" class="form-control"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Role</label><select name="role" class="form-select" required><option value="">Select Role</option>@foreach($roles as $role)<option value="{{ $role->value }}" {{ old('role') == $role->value ? 'selected' : '' }}>{{ ucfirst($role->value) }}</option>@endforeach</select></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active" {{ old('status')=='active' ? 'selected' : '' }}>Active</option><option value="created" {{ old('status')=='created' ? 'selected' : '' }}>Created</option><option value="suspended" {{ old('status')=='suspended' ? 'selected' : '' }}>Suspended</option></select></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Can Login</label><select name="can_login" class="form-select"><option value="0" {{ old('can_login')=='0' ? 'selected' : '' }}>No</option><option value="1" {{ old('can_login')=='1' ? 'selected' : '' }}>Yes</option></select></div>
                        <div class="col-md-12 mb-3"><label class="form-label">Departments</label><select name="departments[]" class="form-select"><option value="">Select department</option>@foreach($departments as $department)<option value="{{ $department->id }}" {{ in_array($department->id, old('departments', [])) ? 'selected' : '' }}>{{ $department->name }}</option>@endforeach</select><small class="dashboard-form-helper">Hold CTRL (Windows) or CMD (Mac) to select multiple departments if supported by the browser widget.</small></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Day Joined</label><input type="date" name="day_joined" value="{{ old('day_joined') }}" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Birthday</label><input type="date" name="birthday" value="{{ old('birthday') }}" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Occupation</label><input type="text" name="occupation" value="{{ old('occupation') }}" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">State of Origin</label><select name="state_of_origin" class="form-select"><option value="">Select State</option>@foreach($states as $state)<option value="{{ $state }}" {{ old('state_of_origin')==$state ? 'selected' : '' }}>{{ $state }}</option>@endforeach</select></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Hobbies</label><textarea name="hobbies" class="form-control">{{ old('hobbies') }}</textarea></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Favourite Quote</label><textarea name="favourite_quote" class="form-control">{{ old('favourite_quote') }}</textarea></div>
                        <div class="col-md-12 mb-3"><label class="form-label">What Attracted You</label><textarea name="what_attracted_you" class="form-control">{{ old('what_attracted_you') }}</textarea></div>
                    </div>
                    <div class="dashboard-form-actions"><button class="btn btn-primary dashboard-form-primary-btn">Create User</button></div>
                </form>
                <div class="mt-3 dashboard-form-helper">Note: A <strong>default password will be automatically generated</strong> and the user will be required to change it on first login.</div>
            </div></div>
        </div>
    </div>
</div>
