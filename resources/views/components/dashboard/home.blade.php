@include('components.dashboard.partials.listing-shell')

@php
    $pendingTestimonies = $dashboard['latestTestimonies']->where('is_approved', false)->count();
    $mediaCount = $dashboard['latestMedia']->count();
    $upcomingCount = $dashboard['upcomingEvents']->count();
@endphp

<div class="container">
    <div class="page-inner">
        <div class="listing-shell">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="listing-hero card">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="listing-eyebrow">Dashboard Home</span>
                            <h2 class="listing-title">Welcome back, {{ $dashboard['user']->name }}. Here’s a clearer view of what needs your attention today.</h2>
                            <p class="listing-subtitle">Track profile completion, upcoming ministry activity, pending approvals, recent uploads, and the current church focus from one modern workspace.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="listing-hero-actions">
                                <div class="d-flex flex-wrap gap-2">
                                    @if (auth()->user()->isMedia() || auth()->user()->isPastor())
                                        <a href="{{ route('dashboard.stream.index') }}" class="btn btn-danger btn-lg listing-primary-btn">Manage Streams</a>
                                    @endif
                                    <a href="{{ route('settings.index') }}" class="btn btn-primary btn-lg listing-primary-btn">Manage Settings</a>
                                </div>
                                <div class="listing-hero-note"><span class="dot"></span>{{ now()->format('l, d M Y') }} dashboard snapshot</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-xl-3 col-md-6">
                    <div class="listing-stat-card">
                        <div class="listing-stat-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-user-check"></i></div>
                        <div>
                            <div class="listing-stat-value">{{ $dashboard['profileCompletion'] }}%</div>
                            <div class="listing-stat-label">Profile Completion</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="listing-stat-card">
                        <div class="listing-stat-icon" style="background:#eef6ff;color:#1d4ed8;"><i class="fas fa-calendar-days"></i></div>
                        <div>
                            <div class="listing-stat-value">{{ $upcomingCount }}</div>
                            <div class="listing-stat-label">Upcoming Events</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="listing-stat-card">
                        <div class="listing-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-bullhorn"></i></div>
                        <div>
                            <div class="listing-stat-value">{{ $pendingTestimonies }}</div>
                            <div class="listing-stat-label">Pending Testimonies</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="listing-stat-card">
                        <div class="listing-stat-icon" style="background:#ede9fe;color:#6d28d9;"><i class="fas fa-photo-film"></i></div>
                        <div>
                            <div class="listing-stat-value">{{ $mediaCount }}</div>
                            <div class="listing-stat-label">Recent Media Items</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-xl-8">
                    <div class="card listing-library-card">
                        <div class="card-body p-4">
                            <div class="listing-toolbar mb-4">
                                <div>
                                    <h3 class="listing-toolbar-title">Church Focus</h3>
                                    <p class="listing-toolbar-subtitle mb-0">A quick read on the current year and month emphasis for the parish.</p>
                                </div>
                                <div class="listing-toolbar-badge">Vision Alignment</div>
                            </div>

                            @if ($dashboard['yearlyDetail'])
                                <div class="dashboard-home-focus-grid">
                                    <div class="dashboard-home-focus-card">
                                        <span class="dashboard-home-focus-label">Theme For {{ $dashboard['yearlyDetail']->current_year }}</span>
                                        <h4>{{ $dashboard['yearlyDetail']->year_theme }}</h4>
                                        <p class="mb-2"><strong>{{ $dashboard['yearlyDetail']->year_scripture }}</strong></p>
                                        <p class="mb-0">{{ $dashboard['yearlyDetail']->year_scripture_content }}</p>
                                    </div>
                                    <div class="dashboard-home-focus-card soft">
                                        <span class="dashboard-home-focus-label">Theme For {{ $dashboard['yearlyDetail']->current_month }}</span>
                                        <h4>{{ $dashboard['yearlyDetail']->current_month_theme }}</h4>
                                        <p class="mb-2"><strong>{{ $dashboard['yearlyDetail']->current_month_scripture }}</strong></p>
                                        <p class="mb-0">{{ $dashboard['yearlyDetail']->current_month_scripture_content }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="listing-empty-state">
                                    <div class="listing-empty-icon"><i class="fas fa-seedling"></i></div>
                                    <h4 class="mb-2">Yearly details are not set yet</h4>
                                    <p class="text-muted mb-3">Add the yearly and monthly church focus in settings to make this dashboard more useful for the team.</p>
                                    <a href="{{ route('settings.index') }}" class="btn btn-primary">Open Settings</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card listing-library-card mt-4">
                        <div class="card-body p-4">
                            <div class="listing-toolbar mb-4">
                                <div>
                                    <h3 class="listing-toolbar-title">Latest Sermon</h3>
                                    <p class="listing-toolbar-subtitle mb-0">Keep the most recent teaching within quick reach for review or sharing.</p>
                                </div>
                                <a href="{{ route('dashboard.sermons.index') }}" class="listing-toolbar-badge text-decoration-none">View Sermons</a>
                            </div>

                            @if ($dashboard['latestSermon'])
                                <div class="dashboard-home-feature-card">
                                    <div class="dashboard-home-feature-icon"><i class="fas fa-microphone-lines"></i></div>
                                    <div class="dashboard-home-feature-body">
                                        <h4>{{ $dashboard['latestSermon']->title }}</h4>
                                        <div class="listing-main-meta mb-3">
                                            <span>Speaker: {{ $dashboard['latestSermon']->speaker->name ?? 'Unknown speaker' }}</span>
                                            <span>{{ $dashboard['latestSermon']->sermon_date?->format('d M, Y') }}</span>
                                        </div>
                                        <p class="mb-3 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($dashboard['latestSermon']->description), 180) }}</p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="{{ route('dashboard.sermons.index') }}" class="btn btn-primary">Manage Sermons</a>
                                            <a href="{{ route('sermons') }}" target="_blank" class="btn btn-outline-secondary">Open Public Sermons</a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="listing-empty-state">
                                    <div class="listing-empty-icon"><i class="fas fa-microphone-lines"></i></div>
                                    <h4 class="mb-2">No sermons available yet</h4>
                                    <p class="text-muted mb-0">Sermons will appear here once the first teaching record is created.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if (auth()->user()->isAdmin() || auth()->user()->isPastor() || auth()->user()->isMedia() || auth()->user()->isEditor())
                        <div class="card listing-library-card mt-4">
                            <div class="card-body p-4">
                                <div class="listing-toolbar mb-4">
                                    <div>
                                        <h3 class="listing-toolbar-title">Latest Gallery Additions</h3>
                                        <p class="listing-toolbar-subtitle mb-0">Recently uploaded media with the new normalized visual presentation.</p>
                                    </div>
                                    <a href="{{ route('dashboard.media.index') }}" class="listing-toolbar-badge text-decoration-none">Media Library</a>
                                </div>

                                @if ($dashboard['latestMedia']->count())
                                    <div class="dashboard-home-media-grid">
                                        @foreach ($dashboard['latestMedia'] as $media)
                                            <a href="{{ route('dashboard.media.index') }}" class="dashboard-home-media-tile">
                                                <img src="{{ $media->visual_url }}" alt="{{ $media->title }}">
                                                <span>{{ $media->title }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="listing-empty-state">
                                        <div class="listing-empty-icon"><i class="fas fa-images"></i></div>
                                        <h4 class="mb-2">No recent media uploads</h4>
                                        <p class="text-muted mb-0">Media added by the team will appear here for quick review.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-xl-4">
                    <div class="card listing-library-card">
                        <div class="card-body p-4">
                            <div class="listing-toolbar mb-4">
                                <div>
                                    <h3 class="listing-toolbar-title">Quick Actions</h3>
                                    <p class="listing-toolbar-subtitle mb-0">Jump into frequent tasks without digging through navigation.</p>
                                </div>
                            </div>
                            <div class="dashboard-home-action-stack">
                                <a href="{{ route('dashboard.testimonies.create') }}" class="dashboard-home-action-card">
                                    <i class="fas fa-comment-dots"></i>
                                    <div>
                                        <strong>Submit Testimony</strong>
                                        <span>Add a new testimony record</span>
                                    </div>
                                </a>
                                <a href="{{ route('dashboard.announcements.create') }}" class="dashboard-home-action-card">
                                    <i class="fas fa-bullhorn"></i>
                                    <div>
                                        <strong>Submit Announcement</strong>
                                        <span>Create a new active announcement</span>
                                    </div>
                                </a>
                                <a href="{{ route('dashboard.events.index') }}" class="dashboard-home-action-card">
                                    <i class="fas fa-calendar-plus"></i>
                                    <div>
                                        <strong>Manage Events</strong>
                                        <span>Review schedules and upcoming programs</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card listing-library-card mt-4">
                        <div class="card-body p-4">
                            <div class="listing-toolbar mb-4">
                                <div>
                                    <h3 class="listing-toolbar-title">Newest Member</h3>
                                    <p class="listing-toolbar-subtitle mb-0">The latest person added to the church community records.</p>
                                </div>
                            </div>

                            @if ($dashboard['latestUser'])
                                <div class="dashboard-home-member-card">
                                    <img src="{{ $dashboard['latestUser']->avatar_url }}" alt="{{ $dashboard['latestUser']->name }}" class="listing-thumb-avatar">
                                    <div>
                                        <h4 class="mb-1">{{ $dashboard['latestUser']->name }}</h4>
                                        <div class="listing-main-meta">
                                            <span>{{ $dashboard['latestUser']->email }}</span>
                                            <span>Joined {{ $dashboard['latestUser']->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted mb-0">No members found yet.</p>
                            @endif
                        </div>
                    </div>

                    <div class="card listing-library-card mt-4">
                        <div class="card-body p-4">
                            <div class="listing-toolbar mb-4">
                                <div>
                                    <h3 class="listing-toolbar-title">Upcoming Events</h3>
                                    <p class="listing-toolbar-subtitle mb-0">What is coming up next on the church calendar.</p>
                                </div>
                                <a href="{{ route('dashboard.events.index') }}" class="listing-toolbar-badge text-decoration-none">View All</a>
                            </div>

                            @forelse ($dashboard['upcomingEvents'] as $event)
                                <div class="dashboard-home-side-item">
                                    <div class="dashboard-home-side-date">
                                        <strong>{{ $event->event_date->format('d') }}</strong>
                                        <span>{{ $event->event_date->format('M') }}</span>
                                    </div>
                                    <div>
                                        <h4>{{ $event->title }}</h4>
                                        <div class="listing-main-meta">
                                            <span>{{ $event->event_date->format('g:i A') }}</span>
                                            <span>{{ $event->location }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No upcoming events scheduled.</p>
                            @endforelse
                        </div>
                    </div>

                    @if ($dashboard['latestAnnouncement'])
                        <div class="card listing-library-card mt-4 dashboard-home-warning-card">
                            <div class="card-body p-4">
                                <div class="listing-toolbar mb-3">
                                    <div>
                                        <h3 class="listing-toolbar-title">Announcement For Approval</h3>
                                        <p class="listing-toolbar-subtitle mb-0">A pending announcement is waiting for review.</p>
                                    </div>
                                </div>
                                <h4 class="mb-2">{{ $dashboard['latestAnnouncement']->title }}</h4>
                                <p class="text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($dashboard['latestAnnouncement']->content), 120) }}</p>
                                <form action="{{ route('dashboard.announcements.approve', $dashboard['latestAnnouncement']) }}" method="POST" class="d-flex gap-2 flex-wrap">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success">Approve</button>
                                    <a href="{{ route('dashboard.announcements.index') }}" class="btn btn-outline-secondary">Review Queue</a>
                                </form>
                            </div>
                        </div>
                    @endif

                    <div class="card listing-library-card mt-4">
                        <div class="card-body p-4">
                            <div class="listing-toolbar mb-4">
                                <div>
                                    <h3 class="listing-toolbar-title">Testimonies For Approval</h3>
                                    <p class="listing-toolbar-subtitle mb-0">Recent testimony submissions requiring attention.</p>
                                </div>
                                <a href="{{ route('dashboard.testimonies.index') }}" class="listing-toolbar-badge text-decoration-none">View All</a>
                            </div>

                            @forelse ($dashboard['latestTestimonies'] as $testimony)
                                <div class="dashboard-home-side-item align-items-start">
                                    <div class="dashboard-home-side-icon"><i class="fas fa-quote-left"></i></div>
                                    <div class="flex-grow-1">
                                        <h4>{{ $testimony->testifier_name }}</h4>
                                        <p class="text-muted mb-2">"{{ \Illuminate\Support\Str::limit($testimony->content, 90) }}"</p>
                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                            <span class="listing-pill {{ $testimony->is_approved ? 'success' : 'warning' }}">{{ $testimony->is_approved ? 'Approved' : 'Pending' }}</span>
                                            @if (!$testimony->is_approved)
                                                <form action="{{ route('dashboard.testimonies.approve', $testimony) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No testimonies are awaiting approval.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <style>
            .dashboard-home-focus-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 18px;
            }

            .dashboard-home-focus-card,
            .dashboard-home-feature-card,
            .dashboard-home-action-card,
            .dashboard-home-member-card,
            .dashboard-home-side-item,
            .dashboard-home-media-tile {
                border: 1px solid #e2e8f0;
                border-radius: 22px;
                background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            }

            .dashboard-home-focus-card {
                padding: 22px;
            }

            .dashboard-home-focus-card.soft {
                background: linear-gradient(180deg, #f8fafc 0%, #eefdf8 100%);
            }

            .dashboard-home-focus-label {
                display: inline-block;
                margin-bottom: 10px;
                font-size: 0.78rem;
                font-weight: 800;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                color: #0f766e;
            }

            .dashboard-home-focus-card h4,
            .dashboard-home-feature-body h4,
            .dashboard-home-member-card h4,
            .dashboard-home-side-item h4 {
                color: #0f172a;
                font-weight: 800;
                margin-bottom: 8px;
            }

            .dashboard-home-feature-card {
                display: flex;
                gap: 18px;
                padding: 22px;
                align-items: flex-start;
            }

            .dashboard-home-feature-icon,
            .dashboard-home-side-icon {
                width: 56px;
                height: 56px;
                border-radius: 18px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                background: linear-gradient(135deg, #ecfeff, #eef6ff);
                color: #0f766e;
                font-size: 1.2rem;
            }

            .dashboard-home-feature-body {
                flex: 1;
                min-width: 0;
            }

            .dashboard-home-media-grid {
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 14px;
            }

            .dashboard-home-media-tile {
                padding: 10px;
                text-decoration: none;
                color: inherit;
            }

            .dashboard-home-media-tile img {
                width: 100%;
                aspect-ratio: 4 / 3;
                object-fit: cover;
                border-radius: 16px;
                margin-bottom: 10px;
            }

            .dashboard-home-media-tile span {
                display: block;
                font-size: 0.84rem;
                color: #64748b;
                font-weight: 600;
            }

            .dashboard-home-action-stack {
                display: grid;
                gap: 12px;
            }

            .dashboard-home-action-card {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 18px;
                text-decoration: none;
                color: inherit;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .dashboard-home-action-card:hover,
            .dashboard-home-media-tile:hover {
                transform: translateY(-2px);
                box-shadow: 0 16px 30px rgba(15, 23, 42, 0.08);
            }

            .dashboard-home-action-card i {
                width: 46px;
                height: 46px;
                border-radius: 16px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #ecfeff;
                color: #0f766e;
                font-size: 1rem;
                flex-shrink: 0;
            }

            .dashboard-home-action-card strong,
            .dashboard-home-side-item strong {
                display: block;
                color: #0f172a;
            }

            .dashboard-home-action-card span {
                color: #64748b;
                font-size: 0.88rem;
            }

            .dashboard-home-member-card,
            .dashboard-home-side-item {
                display: flex;
                gap: 14px;
                padding: 18px;
            }

            .dashboard-home-side-item + .dashboard-home-side-item {
                margin-top: 12px;
            }

            .dashboard-home-side-date {
                width: 62px;
                min-width: 62px;
                border-radius: 18px;
                padding: 10px;
                text-align: center;
                background: #0f172a;
                color: #fff;
            }

            .dashboard-home-side-date strong {
                display: block;
                font-size: 1.2rem;
                line-height: 1;
            }

            .dashboard-home-side-date span {
                display: block;
                margin-top: 4px;
                font-size: 0.74rem;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }

            .dashboard-home-warning-card {
                border: 1px solid #fde68a;
                background: linear-gradient(180deg, #fffbeb 0%, #fff 100%);
            }

            @media (max-width: 1199.98px) {
                .dashboard-home-media-grid {
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                }
            }

            @media (max-width: 767.98px) {
                .dashboard-home-focus-grid,
                .dashboard-home-media-grid {
                    grid-template-columns: 1fr;
                }

                .dashboard-home-feature-card {
                    flex-direction: column;
                }
            }
        </style>
    @endpush
@endonce
