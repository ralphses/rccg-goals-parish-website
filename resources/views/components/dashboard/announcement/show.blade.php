@props(['announcement'])
@include('components.dashboard.partials.show-shell')

@php
    $canManageAnnouncements = auth()->user()->isAdmin() || auth()->user()->isPastor() || auth()->user()->isMedia();
@endphp

<div class="container">
    <div class="page-inner">
        <div class="show-shell">
            <div class="show-hero card mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="show-eyebrow">Announcement View</span>
                            <h2 class="show-title">{{ $announcement->title }}</h2>
                            <p class="show-subtitle">Review announcement content, schedule, approval state, and attached media from one cleaner detail page.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="show-hero-actions">
                                <div class="show-action-row">
                                    <a href="{{ route('dashboard.announcements.index') }}" class="btn btn-outline-secondary btn-lg">Back to Announcements</a>
                                    @if ($canManageAnnouncements)
                                        <a href="{{ route('dashboard.announcements.edit', $announcement->id) }}" class="btn btn-primary btn-lg show-primary-btn">Edit Announcement</a>
                                    @endif
                                </div>
                                <form action="{{ route('dashboard.announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?')" class="mt-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-lg">Delete Announcement</button>
                                </form>
                                <div class="show-hero-note"><span class="dot"></span>Created {{ $announcement->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="show-stat-card">
                        <div class="show-stat-icon" style="background:#eef6ff;color:#1d4ed8;"><i class="fas fa-calendar-day"></i></div>
                        <div><div class="show-stat-value">{{ $announcement->service_date->format('d M') }}</div><div class="show-stat-label">Service Date</div></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="show-stat-card">
                        <div class="show-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-repeat"></i></div>
                        <div><div class="show-stat-value">{{ $announcement->frequency->name }}</div><div class="show-stat-label">Frequency</div></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="show-stat-card">
                        <div class="show-stat-icon" style="background:{{ $announcement->is_active ? '#dcfce7' : '#fee2e2' }};color:{{ $announcement->is_active ? '#15803d' : '#dc2626' }};"><i class="fas fa-toggle-on"></i></div>
                        <div><div class="show-stat-value">{{ $announcement->is_active ? 'Active' : 'Inactive' }}</div><div class="show-stat-label">Delivery Status</div></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="show-stat-card">
                        <div class="show-stat-icon" style="background:{{ $announcement->is_approved ? '#dcfce7' : '#fef3c7' }};color:{{ $announcement->is_approved ? '#15803d' : '#b45309' }};"><i class="fas fa-badge-check"></i></div>
                        <div><div class="show-stat-value">{{ $announcement->is_approved ? 'Approved' : 'Pending' }}</div><div class="show-stat-label">Approval State</div></div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="show-detail-card">
                        <div class="show-card-header">
                            <h3 class="show-card-title">Announcement Details</h3>
                            <p class="show-card-subtitle">Key schedule, ownership, and content information.</p>
                        </div>
                        <div class="show-card-body">
                            <div class="show-meta-grid mb-4">
                                <div class="show-meta-item"><span class="show-meta-label">Creator</span><div class="show-meta-value">{{ $announcement->creator->name }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Service Date</span><div class="show-meta-value">{{ $announcement->service_date->format('d M, Y') }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Frequency</span><div class="show-meta-value">{{ $announcement->frequency->name }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Approval</span><div class="show-meta-value"><span class="show-pill {{ $announcement->is_approved ? 'success' : 'warning' }}">{{ $announcement->is_approved ? 'Approved' : 'Not Approved' }}</span></div></div>
                            </div>

                            <h4 class="mb-3">Content</h4>
                            <div class="show-content-block">
                                {{ $announcement->content }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="show-side-card">
                        <div class="show-card-header">
                            <h3 class="show-card-title">Attached Media</h3>
                            <p class="show-card-subtitle">Visual assets linked to this announcement.</p>
                        </div>
                        <div class="show-card-body">
                            @if ($announcement->media->isNotEmpty())
                                <div class="show-thumb-list">
                                    @foreach ($announcement->media as $item)
                                        <div class="show-thumb-card">
                                            @if ($item->media_type === \App\enums\MediaType::IMAGE)
                                                <img src="{{ $item->visual_url }}" alt="{{ $item->title }}">
                                            @elseif ($item->media_type === \App\enums\MediaType::VIDEO)
                                                <img src="{{ $item->visual_url }}" alt="{{ $item->title }}">
                                            @endif
                                            <div class="show-thumb-card-body">
                                                <div class="show-thumb-card-title">{{ $item->title }}</div>
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                    <span class="show-pill info">{{ ucwords($item->media_type->value) }}</span>
                                                    <a href="{{ $item->file_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">Open</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="show-empty-state">
                                    <i class="fas fa-photo-film"></i>
                                    <p class="mb-0">No media attached to this announcement.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($canManageAnnouncements && !$announcement->is_approved)
                        <div class="show-side-card mt-4">
                            <div class="show-card-header">
                                <h3 class="show-card-title">Approval Action</h3>
                                <p class="show-card-subtitle">Publish this announcement into the approved flow.</p>
                            </div>
                            <div class="show-card-body">
                                <form action="{{ route('dashboard.announcements.approve', $announcement->id) }}" method="POST" class="show-action-row">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success">Approve Announcement</button>
                                    <a href="{{ route('dashboard.announcements.index') }}" class="btn btn-outline-secondary">Review Queue</a>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
