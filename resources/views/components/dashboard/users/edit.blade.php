@props(['user', 'roles', 'departments', 'states'])
@include('components.dashboard.partials.form-shell')

<div class="container"><div class="page-inner"><div class="dashboard-form-shell">
    <div class="dashboard-form-hero card mb-4"><div class="card-body p-4 p-lg-5"><div class="row align-items-center g-4"><div class="col-lg-8"><span class="dashboard-form-eyebrow">Users Form</span><h2 class="dashboard-form-title">Edit this user with clearer profile, access, and membership controls.</h2><p class="dashboard-form-subtitle">Update the avatar, access settings, church profile details, and department assignments from one refined workspace.</p></div><div class="col-lg-4"><div class="dashboard-form-hero-actions"><a href="{{ route('dashboard.users.index') }}" class="btn btn-outline-secondary btn-lg dashboard-form-secondary-btn">Back to Users</a><div class="dashboard-form-note"><span class="dot"></span>Current avatar stays visible until replaced</div></div></div></div></div></div>
    <div class="card dashboard-form-card"><div class="card-header"><div class="card-title">Edit User</div></div><div class="card-body">
        @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('dashboard.users.update', $user->id) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-12 mb-4">
                    <x-dashboard.partials.cropped-image-field
                        label="Upload New Avatar"
                        source-name="avatar_source"
                        cropped-name="avatar_cropped"
                        source-id="user-avatar-edit"
                        :current-url="$user->avatar_url ?? asset('assets/img/default-avatar.png')"
                        current-label="Current avatar"
                        helper="Optional: replace the current avatar, drag to focus the best section, then confirm the crop."
                        empty-state="Select a new avatar image to begin cropping."
                        result-label="Final avatar"
                        :preview-rounded="true"
                    />
                </div>
                <div class="col-md-6 mb-3"><label class="form-label">Name</label><input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Phone</label><input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Address</label><input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-control"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Role</label><select name="role" class="form-select" required>@foreach ($roles as $role)<option value="{{ $role->value }}" {{ $user->role->value == $role->value ? 'selected' : '' }}>{{ ucfirst($role->value) }}</option>@endforeach</select></div>
                <div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active" {{ $user->status->value == 'active' ? 'selected' : '' }}>Active</option><option value="created" {{ $user->status->value == 'created' ? 'selected' : '' }}>Created</option><option value="suspended" {{ $user->status->value == 'suspended' ? 'selected' : '' }}>Suspended</option></select></div>
                <div class="col-md-12 mb-3"><label class="form-label">Departments</label><select name="departments[]" class="form-select">@foreach ($departments as $department)<option value="{{ $department->id }}" {{ $user->departments->contains($department->id) ? 'selected' : '' }}>{{ $department->name }}</option>@endforeach</select></div>
                <div class="col-md-6 mb-3"><label class="form-label">Day Joined</label><input type="date" name="day_joined" value="{{ old('day_joined', optional($user->day_joined)->format('Y-m-d')) }}" class="form-control"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Birthday</label><input type="date" name="birthday" value="{{ old('birthday', optional($user->birthday)->format('Y-m-d')) }}" class="form-control"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Occupation</label><input type="text" name="occupation" value="{{ old('occupation', $user->occupation) }}" class="form-control"></div>
                <div class="col-md-6 mb-3"><label class="form-label">State of Origin</label><select name="state_of_origin" class="form-select">@foreach ($states as $state)<option value="{{ $state }}" {{ $user->state_of_origin == $state ? 'selected' : '' }}>{{ $state }}</option>@endforeach</select></div>
                <div class="col-md-6 mb-3"><label class="form-label">What Attracted You</label><textarea name="what_attracted_you" class="form-control">{{ old('what_attracted_you', $user->what_attracted_you) }}</textarea></div>
                <div class="col-md-6 mb-3"><label class="form-label">Hobbies</label><textarea name="hobbies" class="form-control">{{ old('hobbies', $user->hobbies) }}</textarea></div>
                <div class="col-md-12 mb-3"><label class="form-label">Favourite Quote</label><textarea name="favourite_quote" class="form-control">{{ old('favourite_quote', $user->favourite_quote) }}</textarea></div>
                <div class="col-md-12 mb-3"><input type="hidden" name="can_login" value="0"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="can_login" value="1" {{ old('can_login', $user->can_login) ? 'checked' : '' }}><label class="form-check-label">Can Login</label></div></div>
            </div>
            <div class="dashboard-form-actions"><button class="btn btn-primary dashboard-form-primary-btn">Update User</button></div>
        </form>
    </div></div>
</div></div></div>
