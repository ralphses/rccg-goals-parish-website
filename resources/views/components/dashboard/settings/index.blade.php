@include('components.dashboard.partials.form-shell')

<div class="container">
    <div class="page-inner">
        <div class="dashboard-form-shell">
            <div class="dashboard-form-hero card mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="dashboard-form-eyebrow">Settings Workspace</span>
                            <h2 class="dashboard-form-title">Manage profile, security, integrations, and church details from one polished settings hub.</h2>
                            <p class="dashboard-form-subtitle">Everything is organized into focused cards so updates feel calmer and easier to review before saving.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="dashboard-form-hero-actions">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg dashboard-form-secondary-btn">Back to Dashboard</a>
                                <div class="dashboard-form-note"><span class="dot"></span>{{ $settings['user']->name }} is currently signed in</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-12">
                    <div class="card dashboard-form-card">
                        <div class="card-header">
                            <div class="card-title">Profile Settings</div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('patch')
                                <div class="row">
                                    <div class="col-md-12 mb-4">
                                        <x-dashboard.partials.cropped-image-field
                                            label="Change Avatar"
                                            source-name="avatar_source"
                                            cropped-name="avatar_cropped"
                                            source-id="settings-avatar"
                                            :current-url="$settings['user']->avatar_url ?? asset('assets/img/default-avatar.png')"
                                            current-label="Current avatar"
                                            helper="Optional: upload a new avatar, drag to choose the best framing, then confirm the crop."
                                            empty-state="Select a profile image to begin cropping."
                                            result-label="Final avatar"
                                            :preview-rounded="true"
                                        />
                                    </div>
                                    <div class="col-md-6"><div class="form-group"><label for="name">Name</label><input type="text" class="form-control" id="name" name="name" value="{{ old('name', $settings['user']->name) }}" required></div></div>
                                    <div class="col-md-6"><div class="form-group"><label for="email">Email</label><input type="email" class="form-control" id="email" value="{{ $settings['user']->email }}" readonly></div></div>
                                    <div class="col-md-6"><div class="form-group"><label for="phone">Phone</label><input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $settings['user']->phone) }}"></div></div>
                                    <div class="col-md-6"><div class="form-group"><label for="birthday">Birthday</label><input type="date" class="form-control" id="birthday" name="birthday" value="{{ old('birthday', $settings['user']->birthday ? $settings['user']->birthday->format('Y-m-d') : '') }}"></div></div>
                                    <div class="col-md-12"><div class="form-group"><label for="address">Address</label><textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $settings['user']->address) }}</textarea></div></div>
                                    <div class="col-md-6"><div class="form-group"><label for="state_of_origin">State of Origin</label><input type="text" class="form-control" id="state_of_origin" name="state_of_origin" value="{{ old('state_of_origin', $settings['user']->state_of_origin) }}"></div></div>
                                    <div class="col-md-6"><div class="form-group"><label for="occupation">Occupation</label><input type="text" class="form-control" id="occupation" name="occupation" value="{{ old('occupation', $settings['user']->occupation) }}"></div></div>
                                    <div class="col-md-12"><div class="form-group"><label for="hobbies">Hobbies</label><input type="text" class="form-control" id="hobbies" name="hobbies" value="{{ old('hobbies', $settings['user']->hobbies) }}"></div></div>
                                    <div class="col-md-12"><div class="form-group"><label for="what_attracted_you">What Attracted You to Church?</label><textarea class="form-control" id="what_attracted_you" name="what_attracted_you" rows="3">{{ old('what_attracted_you', $settings['user']->what_attracted_you) }}</textarea></div></div>
                                    <div class="col-md-12"><div class="form-group"><label for="favourite_quote">Favourite Quote</label><textarea class="form-control" id="favourite_quote" name="favourite_quote" rows="3">{{ old('favourite_quote', $settings['user']->favourite_quote) }}</textarea></div></div>
                                </div>
                                <div class="dashboard-form-actions">
                                    <button type="submit" class="btn btn-primary dashboard-form-primary-btn">Update Profile</button>
                                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Back</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card dashboard-form-card">
                        <div class="card-header"><div class="card-title">Change Password</div></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('settings.password.update') }}">
                                @csrf
                                @method('patch')
                                <div class="row">
                                    <div class="col-md-12"><div class="form-group"><label for="old_password">Old Password</label><input type="password" class="form-control" id="old_password" name="old_password" required></div></div>
                                    <div class="col-md-6"><div class="form-group"><label for="new_password">New Password</label><input type="password" class="form-control" id="new_password" name="new_password" required></div></div>
                                    <div class="col-md-6"><div class="form-group"><label for="new_password_confirmation">Confirm New Password</label><input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required></div></div>
                                </div>
                                <div class="dashboard-form-actions">
                                    <button type="submit" class="btn btn-primary dashboard-form-primary-btn">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @if (auth()->user()->isAdmin())
                    <div class="col-12">
                        <div class="card dashboard-form-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="card-title">YouTube Integration</div>
                                @if ($settings['youtubeIntegration'])
                                    <span class="badge bg-success">Connected</span>
                                @else
                                    <span class="badge bg-warning text-dark">Not Connected</span>
                                @endif
                            </div>
                            <div class="card-body">
                                @if ($settings['youtubeIntegration'])
                                    <div class="dashboard-form-preview-panel">
                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                            @if ($settings['youtubeIntegration']->channel_thumbnail_url)
                                                <img src="{{ $settings['youtubeIntegration']->channel_thumbnail_url }}" alt="{{ $settings['youtubeIntegration']->channel_title }}" width="56" height="56" class="rounded-circle border">
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $settings['youtubeIntegration']->channel_title }}</div>
                                                <div class="text-muted small">{{ $settings['youtubeIntegration']->channel_id }}</div>
                                                <div class="text-muted small">Last used: {{ $settings['youtubeIntegration']->last_used_at?->diffForHumans() ?? 'Not yet used' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($settings['youtubeIntegration']->last_error)
                                        <div class="alert alert-warning mt-3 mb-0">{{ $settings['youtubeIntegration']->last_error }}</div>
                                    @endif
                                    <form method="POST" action="{{ route('settings.youtube.disconnect') }}" class="mt-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Disconnect the shared YouTube channel?')">Disconnect YouTube</button>
                                    </form>
                                @else
                                    <p class="text-muted mb-3">Connect the shared church YouTube channel to allow background publishing for uploaded dashboard videos.</p>
                                    <a href="{{ route('settings.youtube.connect') }}" class="btn btn-danger"><i class="fab fa-youtube me-2"></i>Connect YouTube Channel</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                    <div class="col-12">
                        <div class="card dashboard-form-card">
                            <div class="card-header"><div class="card-title">Yearly Details</div></div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('settings.yearly-details.update') }}">
                                    @csrf
                                    @method('patch')
                                    <div class="row">
                                        <div class="col-md-6"><div class="form-group"><label for="current_year">Current Year</label><select class="form-control" id="current_year" name="current_year" required>@foreach ($settings['years'] as $year)<option value="{{ $year }}" {{ old('current_year', $settings['yearlyDetail']->current_year) == $year ? 'selected' : '' }}>{{ $year }}</option>@endforeach</select></div></div>
                                        <div class="col-md-6"><div class="form-group"><label for="current_month">Current Month</label><select class="form-control" id="current_month" name="current_month" required>@foreach ($settings['months'] as $month)<option value="{{ $month }}" {{ old('current_month', $settings['yearlyDetail']->current_month) == $month ? 'selected' : '' }}>{{ $month }}</option>@endforeach</select></div></div>
                                        <div class="col-md-6"><div class="form-group"><label for="year_theme">Year Theme</label><input type="text" class="form-control" id="year_theme" name="year_theme" value="{{ old('year_theme', $settings['yearlyDetail']->year_theme) }}" required></div></div>
                                        <div class="col-md-6"><div class="form-group"><label for="year_scripture">Year Scripture</label><input type="text" class="form-control" id="year_scripture" name="year_scripture" value="{{ old('year_scripture', $settings['yearlyDetail']->year_scripture) }}" required></div></div>
                                        <div class="col-md-12"><div class="form-group"><label for="year_scripture_content">Year Scripture Content</label><textarea class="form-control" id="year_scripture_content" name="year_scripture_content" rows="3">{{ old('year_scripture_content', $settings['yearlyDetail']->year_scripture_content) }}</textarea></div></div>
                                        <div class="col-md-6"><div class="form-group"><label for="current_month_theme">Current Month Theme</label><input type="text" class="form-control" id="current_month_theme" name="current_month_theme" value="{{ old('current_month_theme', $settings['yearlyDetail']->current_month_theme) }}" required></div></div>
                                        <div class="col-md-6"><div class="form-group"><label for="current_month_scripture">Current Month Scripture</label><input type="text" class="form-control" id="current_month_scripture" name="current_month_scripture" value="{{ old('current_month_scripture', $settings['yearlyDetail']->current_month_scripture) }}" required></div></div>
                                        <div class="col-md-12"><div class="form-group"><label for="current_month_scripture_content">Current Month Scripture Content</label><textarea class="form-control" id="current_month_scripture_content" name="current_month_scripture_content" rows="3">{{ old('current_month_scripture_content', $settings['yearlyDetail']->current_month_scripture_content) }}</textarea></div></div>
                                    </div>
                                    <div class="dashboard-form-actions">
                                        <button type="submit" class="btn btn-primary dashboard-form-primary-btn">Update Yearly Details</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
