@props(['user'])
@include('components.dashboard.partials.show-shell')

<div class="container">
    <div class="page-inner">
        <div class="show-shell">
            <div class="show-hero card mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="show-eyebrow">User View</span>
                            <h2 class="show-title">{{ $user->name }}</h2>
                            <p class="show-subtitle">Review membership details, profile completeness signals, department assignments, and account state from one polished user detail page.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="show-hero-actions">
                                <div class="show-action-row">
                                    <a href="{{ route('dashboard.users.index') }}" class="btn btn-outline-secondary btn-lg">Back to Users</a>
                                    <a href="{{ route('dashboard.users.edit', $user->id) }}" class="btn btn-primary btn-lg show-primary-btn">Edit User</a>
                                </div>
                                <div class="show-hero-note"><span class="dot"></span>Account created {{ $user->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#eef6ff;color:#1d4ed8;"><i class="fas fa-user-shield"></i></div><div><div class="show-stat-value">{{ ucfirst($user->role->value) }}</div><div class="show-stat-label">Role</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:{{ $user->isActive() ? '#dcfce7' : '#fee2e2' }};color:{{ $user->isActive() ? '#15803d' : '#dc2626' }};"><i class="fas fa-signal"></i></div><div><div class="show-stat-value">{{ ucfirst($user->status->value) }}</div><div class="show-stat-label">Account Status</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-building-user"></i></div><div><div class="show-stat-value">{{ $user->departments->count() }}</div><div class="show-stat-label">Departments</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#f8fafc;color:#475569;"><i class="fas fa-clock-rotate-left"></i></div><div><div class="show-stat-value">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</div><div class="show-stat-label">Last Login</div></div></div></div>
            </div>

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="show-detail-card">
                        <div class="show-card-header">
                            <h3 class="show-card-title">User Profile</h3>
                            <p class="show-card-subtitle">Personal details, contact data, role, and profile information.</p>
                        </div>
                        <div class="show-card-body">
                            <div class="d-flex align-items-center gap-3 flex-wrap mb-4">
                                <img src="{{ $user->avatar_url }}" width="120" height="120" class="rounded-circle border object-fit-cover" alt="{{ $user->name }}">
                                <div>
                                    <h3 class="mb-1">{{ $user->name }}</h3>
                                    <p class="text-muted mb-1">{{ $user->email }}</p>
                                    <span class="show-pill info">{{ ucfirst($user->role->value) }}</span>
                                </div>
                            </div>

                            <div class="show-meta-grid">
                                <div class="show-meta-item"><span class="show-meta-label">Phone</span><div class="show-meta-value">{{ $user->phone ?? 'N/A' }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Address</span><div class="show-meta-value">{{ $user->address ?? 'N/A' }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Day Joined</span><div class="show-meta-value">{{ $user->day_joined ? $user->day_joined->format('d M Y') : 'N/A' }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Birthday</span><div class="show-meta-value">{{ $user->birthday ? $user->birthday->format('M d') : 'N/A' }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">What Attracted You</span><div class="show-meta-value">{{ $user->what_attracted_you ?? 'N/A' }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">State Of Origin</span><div class="show-meta-value">{{ $user->state_of_origin ?? 'N/A' }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Occupation</span><div class="show-meta-value">{{ $user->occupation ?? 'N/A' }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Hobbies</span><div class="show-meta-value">{{ $user->hobbies ?? 'N/A' }}</div></div>
                            </div>

                            <div class="mt-4">
                                <h4 class="mb-3">Favourite Quote</h4>
                                <div class="show-content-block">{{ $user->favourite_quote ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="show-side-card">
                        <div class="show-card-header">
                            <h3 class="show-card-title">Department Membership</h3>
                            <p class="show-card-subtitle">The teams this user currently belongs to.</p>
                        </div>
                        <div class="show-card-body">
                            @forelse ($user->departments as $department)
                                <div class="show-side-item">
                                    <div class="show-side-icon"><i class="fas fa-people-group"></i></div>
                                    <div>
                                        <h6>{{ $department->name }}</h6>
                                        <small>{{ ucfirst((string) $department->status) }}</small>
                                    </div>
                                </div>
                            @empty
                                <div class="show-empty-state">
                                    <i class="fas fa-building-circle-xmark"></i>
                                    <p class="mb-0">No departments assigned.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
