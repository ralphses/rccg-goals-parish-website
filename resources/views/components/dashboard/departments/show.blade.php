@props(['department'])
@include('components.dashboard.partials.show-shell')

<div class="container">
    <div class="page-inner">
        <div class="show-shell">
            <div class="show-hero card mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="show-eyebrow">Department View</span>
                            <h2 class="show-title">{{ $department->name }}</h2>
                            <p class="show-subtitle">Review the department’s leadership, membership, cover image, and current operating details from one modern detail page.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="show-hero-actions">
                                <div class="show-action-row">
                                    <a href="{{ route('dashboard.departments.index') }}" class="btn btn-outline-secondary btn-lg">Back to Departments</a>
                                    @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                        <a href="{{ route('dashboard.departments.edit', $department->id) }}" class="btn btn-primary btn-lg show-primary-btn">Edit Department</a>
                                    @endif
                                </div>
                                <div class="show-hero-note"><span class="dot"></span>{{ $department->users->count() }} assigned members</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-xl-4 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#eef6ff;color:#1d4ed8;"><i class="fas fa-user-tie"></i></div><div><div class="show-stat-value">{{ $department->leader?->name ?? 'N/A' }}</div><div class="show-stat-label">Department Leader</div></div></div></div>
                <div class="col-xl-4 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-users"></i></div><div><div class="show-stat-value">{{ $department->users->count() }}</div><div class="show-stat-label">Assigned Members</div></div></div></div>
                <div class="col-xl-4 col-md-12"><div class="show-stat-card"><div class="show-stat-icon" style="background:#f8fafc;color:#475569;"><i class="fas fa-signal"></i></div><div><div class="show-stat-value">{{ ucfirst((string) $department->status) }}</div><div class="show-stat-label">Current Status</div></div></div></div>
            </div>

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="show-detail-card">
                        <div class="show-card-header">
                            <h3 class="show-card-title">Department Overview</h3>
                            <p class="show-card-subtitle">Leadership, status, and description details.</p>
                        </div>
                        <div class="show-card-body">
                            <div class="show-media-frame visual mb-4">
                                @if ($department->image_url)
                                    <img src="{{ $department->image_url }}" alt="{{ $department->name }}">
                                @else
                                    <div class="show-empty-state"><i class="fas fa-image"></i><p class="mb-0">No cover image uploaded</p></div>
                                @endif
                            </div>

                            <div class="show-meta-grid mb-4">
                                <div class="show-meta-item"><span class="show-meta-label">Name</span><div class="show-meta-value">{{ $department->name }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Status</span><div class="show-meta-value"><span class="show-pill info">{{ ucfirst((string) $department->status) }}</span></div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Leader</span><div class="show-meta-value">{{ $department->leader?->name ?? 'No leader assigned' }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Members</span><div class="show-meta-value">{{ $department->users->count() }}</div></div>
                            </div>

                            <h4 class="mb-3">Description</h4>
                            <div class="show-content-block">{!! $department->description !!}</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="show-side-card">
                        <div class="show-card-header">
                            <h3 class="show-card-title">Assigned Members</h3>
                            <p class="show-card-subtitle">People currently connected to this department.</p>
                        </div>
                        <div class="show-card-body">
                            @forelse ($department->users as $user)
                                <div class="show-side-item">
                                    <div class="show-side-icon"><i class="fas fa-user"></i></div>
                                    <div>
                                        <h6>{{ $user->name }}</h6>
                                        <small>{{ $user->email }}</small>
                                    </div>
                                </div>
                            @empty
                                <div class="show-empty-state">
                                    <i class="fas fa-users-slash"></i>
                                    <p class="mb-0">No members assigned to this department yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
