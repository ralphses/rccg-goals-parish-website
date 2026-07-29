@props(['department', 'users'])
@include('components.dashboard.partials.form-shell')

<div class="container"><div class="page-inner"><div class="dashboard-form-shell">
    <div class="dashboard-form-hero card mb-4"><div class="card-body p-4 p-lg-5"><div class="row align-items-center g-4"><div class="col-lg-8"><span class="dashboard-form-eyebrow">Departments Form</span><h2 class="dashboard-form-title">Update this department with clearer leadership and membership controls.</h2><p class="dashboard-form-subtitle">Keep the current cover image, team composition, status, and leader assignment visible while editing.</p></div><div class="col-lg-4"><div class="dashboard-form-hero-actions"><a href="{{ route('dashboard.departments.index') }}" class="btn btn-outline-secondary btn-lg dashboard-form-secondary-btn">Back to Departments</a><div class="dashboard-form-note"><span class="dot"></span>Current cover image remains visible until replaced</div></div></div></div></div></div>
    <div class="card dashboard-form-card"><div class="card-header"><div class="card-title">Edit Department</div></div><div class="card-body">
        @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('dashboard.departments.update', $department->id) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-12 mb-4">
                    <x-dashboard.partials.cropped-image-field
                        label="Cover Image"
                        source-name="image_source"
                        cropped-name="image_cropped"
                        source-id="department-image-edit"
                        :current-url="$department->image_url"
                        current-label="Current cover image"
                        helper="Optional: replace the current cover image, drag to refine the framing, then confirm the crop."
                        empty-state="Select a new department image to begin cropping."
                        result-label="Final department cover"
                    />
                </div>
                <div class="col-md-6 mb-3"><label class="form-label">Name</label><input type="text" name="name" value="{{ old('name', $department->name) }}" class="form-control" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active" {{ old('status', $department->status) == 'active' ? 'selected' : '' }}>Active</option><option value="created" {{ old('status', $department->status) == 'created' ? 'selected' : '' }}>Created</option><option value="suspended" {{ old('status', $department->status) == 'suspended' ? 'selected' : '' }}>Suspended</option></select></div>
                <div class="col-md-12 mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="5">{{ old('description', $department->description) }}</textarea></div>
                <div class="col-md-12 mb-4">
                    <div class="dashboard-form-preview-panel h-100">
                        <p class="mb-2 fw-semibold">SEO Settings</p>
                        <p class="dashboard-form-helper mb-3">Optional overrides for the public department metadata.</p>
                        <div class="row">
                            <div class="col-md-12 mb-3"><label class="form-label">Meta Title</label><input type="text" name="meta_title" value="{{ old('meta_title', $department->meta_title) }}" class="form-control" maxlength="255"></div>
                            <div class="col-md-12 mb-3"><label class="form-label">Meta Description</label><textarea name="meta_description" class="form-control" rows="3" maxlength="320">{{ old('meta_description', $department->meta_description) }}</textarea></div>
                            <div class="col-md-12 mb-0"><label class="form-label">Meta Keywords</label><input type="text" name="meta_keywords" value="{{ old('meta_keywords', $department->meta_keywords) }}" class="form-control" maxlength="255"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-3"><label class="form-label">Department Members</label><select name="users[]" class="form-select" multiple size="8">@php($selectedUsers = old('users', $department->users->pluck('id')->all()))@foreach ($users as $user)<option value="{{ $user->id }}" {{ in_array($user->id, $selectedUsers) ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>@endforeach</select><small class="dashboard-form-helper">Hold CTRL (Windows) or CMD (Mac) to select multiple members.</small></div>
                <div class="col-md-12 mb-3"><label class="form-label">Department Leader</label><select name="leader_id" class="form-select"><option value="">Select a leader</option>@foreach ($users as $user)<option value="{{ $user->id }}" {{ (string) old('leader_id', $department->leader_id) === (string) $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>@endforeach</select><small class="dashboard-form-helper">Optional: assign any user on the platform as department leader.</small></div>
            </div>
            <div class="dashboard-form-actions"><button class="btn btn-primary dashboard-form-primary-btn">Update Department</button></div>
        </form>
    </div></div>
</div></div></div>
