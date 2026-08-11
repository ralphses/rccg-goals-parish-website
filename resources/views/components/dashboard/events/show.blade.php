@props(['event'])
@include('components.dashboard.partials.show-shell')

@php
    function dashboardEventYoutubeId($url)
    {
        preg_match('/(youtube\.com\/(watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches);
        return $matches[3] ?? null;
    }
@endphp

<div class="container">
    <div class="page-inner">
        <div class="show-shell">
            <div class="show-hero card mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="show-eyebrow">Event View</span>
                            <h2 class="show-title">{{ $event->title }}</h2>
                            <p class="show-subtitle">See the event schedule, department link, media, and descriptive content from a cleaner management view.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="show-hero-actions">
                                <div class="show-action-row">
                                    <a href="{{ route('dashboard.events.index') }}" class="btn btn-outline-secondary btn-lg">Back to Events</a>
                                    @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                        <a href="{{ route('dashboard.events.edit', $event->id) }}" class="btn btn-primary btn-lg show-primary-btn">Edit Event</a>
                                    @endif
                                </div>
                                <div class="show-hero-note"><span class="dot"></span>{{ $event->event_date->isFuture() ? 'Upcoming event' : 'Past event' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#eef6ff;color:#1d4ed8;"><i class="fas fa-calendar-day"></i></div><div><div class="show-stat-value">{{ $event->event_date->format('d M') }}</div><div class="show-stat-label">Event Date</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-clock"></i></div><div><div class="show-stat-value">{{ $event->event_date->format('g:i A') }}</div><div class="show-stat-label">Start Time</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-building"></i></div><div><div class="show-stat-value">{{ $event->department->name ?? 'N/A' }}</div><div class="show-stat-label">Department</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#f8fafc;color:#475569;"><i class="fas fa-signal"></i></div><div><div class="show-stat-value">{{ ucfirst((string) $event->status) }}</div><div class="show-stat-label">Status</div></div></div></div>
            </div>

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="show-detail-card">
                        <div class="show-card-header">
                            <h3 class="show-card-title">Event Overview</h3>
                            <p class="show-card-subtitle">Primary event details, schedule, and description.</p>
                        </div>
                        <div class="show-card-body">
                            @if ($event->image_url)
                                <div class="show-media-frame visual mb-4">
                                    <img src="{{ $event->image_url }}" alt="{{ $event->title }}">
                                </div>
                            @endif

                            <div class="show-meta-grid mb-4">
                                <div class="show-meta-item"><span class="show-meta-label">Title</span><div class="show-meta-value">{{ $event->title }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Location</span><div class="show-meta-value">{{ $event->location ?? 'N/A' }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Date</span><div class="show-meta-value">{{ $event->event_date->format('d M, Y') }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Description Heading</span><div class="show-meta-value">{{ $event->description_heading ?? 'N/A' }}</div></div>
                            </div>

                            <h4 class="mb-3">Description</h4>
                            <div class="show-content-block">{!! $event->description ?? 'N/A' !!}</div>
                        </div>
                    </div>

                    @if ($event->video_link)
                        <div class="show-detail-card mt-4">
                            <div class="show-card-header">
                                <h3 class="show-card-title">Event Video</h3>
                                <p class="show-card-subtitle">Embedded preview linked to this event.</p>
                            </div>
                            <div class="show-card-body">
                                <div class="show-media-frame video">
                                    @if (dashboardEventYoutubeId($event->video_link))
                                        <iframe src="https://www.youtube.com/embed/{{ dashboardEventYoutubeId($event->video_link) }}" allowfullscreen></iframe>
                                    @else
                                        <video controls preload="metadata" class="w-100 rounded-4">
                                            <source src="{{ $event->video_link }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-xl-4">
                    <div class="show-side-card">
                        <div class="show-card-header">
                            <h3 class="show-card-title">Event Summary</h3>
                            <p class="show-card-subtitle">Quick operational details for the team.</p>
                        </div>
                        <div class="show-card-body">
                            <div class="show-side-stack">
                                <div class="show-side-item"><div class="show-side-icon"><i class="fas fa-location-dot"></i></div><div><h6>Venue</h6><p>{{ $event->location ?? 'N/A' }}</p></div></div>
                                <div class="show-side-item"><div class="show-side-icon"><i class="fas fa-users-gear"></i></div><div><h6>Department</h6><p>{{ $event->department->name ?? 'Not assigned' }}</p></div></div>
                                <div class="show-side-item"><div class="show-side-icon"><i class="fas fa-signal"></i></div><div><h6>Status</h6><p>{{ ucfirst((string) $event->status) }}</p></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
