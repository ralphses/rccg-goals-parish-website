@props(['testimony'])
@include('components.dashboard.partials.show-shell')

@php
    $canModerateTestimonies = auth()->user()->isAdmin() || auth()->user()->isPastor();
@endphp

<div class="container">
    <div class="page-inner">
        <div class="show-shell">
            <div class="show-hero card mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="show-eyebrow">Testimony View</span>
                            <h2 class="show-title">{{ $testimony->title }}</h2>
                            <p class="show-subtitle">Review the testimony story, contact context, attached media, and approval readiness from a clearer detail page.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="show-hero-actions">
                                <div class="show-action-row">
                                    <a href="{{ route('dashboard.testimonies.index') }}" class="btn btn-outline-secondary btn-lg">Back to Testimonies</a>
                                    @if ($canModerateTestimonies)
                                        <a href="{{ route('dashboard.testimonies.edit', $testimony->id) }}" class="btn btn-primary btn-lg show-primary-btn">Edit Testimony</a>
                                    @endif
                                </div>
                                <form action="{{ route('dashboard.testimonies.destroy', $testimony->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this testimony?')" class="mt-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-lg">Delete Testimony</button>
                                </form>
                                <div class="show-hero-note"><span class="dot"></span>Submitted {{ $testimony->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#eef6ff;color:#1d4ed8;"><i class="fas fa-user"></i></div><div><div class="show-stat-value">{{ $testimony->testifier_name }}</div><div class="show-stat-label">Testifier</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-bullhorn"></i></div><div><div class="show-stat-value">{{ $testimony->announcement_type->name }}</div><div class="show-stat-label">Announcement Type</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:{{ $testimony->is_approved ? '#dcfce7' : '#fef3c7' }};color:{{ $testimony->is_approved ? '#15803d' : '#b45309' }};"><i class="fas fa-badge-check"></i></div><div><div class="show-stat-value">{{ $testimony->is_approved ? 'Approved' : 'Pending' }}</div><div class="show-stat-label">Approval State</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#f8fafc;color:#475569;"><i class="fas fa-photo-film"></i></div><div><div class="show-stat-value">{{ $testimony->media->count() }}</div><div class="show-stat-label">Attached Media</div></div></div></div>
            </div>

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="show-detail-card">
                        <div class="show-card-header">
                            <h3 class="show-card-title">Testimony Details</h3>
                            <p class="show-card-subtitle">Personal details, submission type, and story content.</p>
                        </div>
                        <div class="show-card-body">
                            <div class="show-meta-grid mb-4">
                                <div class="show-meta-item"><span class="show-meta-label">Testifier</span><div class="show-meta-value">{{ $testimony->testifier_name }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Email</span><div class="show-meta-value">{{ $testimony->testifier_email ?? 'N/A' }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Phone</span><div class="show-meta-value">{{ $testimony->testifier_phone ?? 'N/A' }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Type</span><div class="show-meta-value"><span class="show-pill info">{{ $testimony->announcement_type->name }}</span></div></div>
                            </div>

                            @if ($testimony->content)
                                <h4 class="mb-3">Content</h4>
                                <div class="show-content-block">{{ $testimony->content }}</div>
                            @else
                                <div class="show-empty-state">
                                    <i class="fas fa-align-left"></i>
                                    <p class="mb-0">This testimony uses media without a text body.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="show-side-card">
                        <div class="show-card-header">
                            <h3 class="show-card-title">Attached Media</h3>
                            <p class="show-card-subtitle">Images, videos, or audio submitted with this testimony.</p>
                        </div>
                        <div class="show-card-body">
                            @if ($testimony->media->isNotEmpty())
                                <div class="show-thumb-list">
                                    @foreach ($testimony->media as $item)
                                        <div class="show-thumb-card">
                                            @if ($item->media_type === \App\enums\MediaType::IMAGE)
                                                <img src="{{ $item->visual_url }}" alt="{{ $item->title }}">
                                            @elseif ($item->media_type === \App\enums\MediaType::VIDEO)
                                                <img src="{{ $item->visual_url }}" alt="{{ $item->title }}">
                                            @elseif ($item->media_type === \App\enums\MediaType::AUDIO)
                                                <div class="show-thumb-card-body">
                                                    <audio controls class="w-100">
                                                        <source src="{{ $item->file_url }}">
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                </div>
                                            @endif
                                            @if ($item->media_type !== \App\enums\MediaType::AUDIO)
                                                <div class="show-thumb-card-body">
                                                    <div class="show-thumb-card-title">{{ $item->title }}</div>
                                                    <span class="show-pill info">{{ ucwords($item->media_type->value) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="show-empty-state">
                                    <i class="fas fa-photo-film"></i>
                                    <p class="mb-0">No media attached to this testimony.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
