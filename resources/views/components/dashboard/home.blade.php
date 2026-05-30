<div class="container">
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    <div class="page-inner">
        <!-- Welcome Header -->
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Welcome, {{ $dashboard['user']->name }}!</h3>
                <h6 class="op-7 mb-2">Here's a snapshot of your church community.</h6>
            </div>
            <div class="ms-md-auto py-2 py-md-0">
                <a href="{{ route('dashboard.stream.index') }}" class="btn btn-round" style="background-color: #FF0000; color: white;">Manage Streams</a>
                <a href="{{ route('settings.index') }}" class="btn btn-primary btn-round">Manage Settings</a>
            </div>
        </div>

        <!-- Top Row Stats -->
        <div class="row">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-title">Profile Completion</div>
                </div>
                <div class="card-body">
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Profile Completion</p>
                            <h4 class="card-title">{{ $dashboard['profileCompletion'] }}%</h4>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $dashboard['profileCompletion'] }}%;"
                            aria-valuenow="{{ $dashboard['profileCompletion'] }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">

                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-title">Newest Member</div>
                    </div>
                    <div class="card-body">
                        @if ($dashboard['latestUser'])
                            <div class="d-flex">
                                <div class="avatar avatar-lg">
                                    <img src="{{ $dashboard['latestUser']->avatar ? asset('storage/' . $dashboard['latestUser']->avatar) : 'https://via.placeholder.com/150' }}"
                                        alt="..." class="avatar-img rounded-circle">
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0">{{ $dashboard['latestUser']->name }}</h5>
                                    <small class="text-muted">Joined
                                        {{ $dashboard['latestUser']->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
            </div>

                <div class="card card-round">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">Upcoming Events</div>
                        <a href="{{ route('dashboard.events.index') }}" class="btn btn-link btn-sm">View All</a>
                    </div>
                    <div class="card-body">
                        @forelse($dashboard['upcomingEvents'] as $event)
                            <p><strong>{{ $event->title }}</strong><br>{{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}
                            </p>
                            @if (!$loop->last)
                                <hr>
                            @endif
                        @empty
                            <p>No upcoming events scheduled.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <!-- Main Content Area -->
        <div class="row mt-md-n4">
            <div class="col-md-8">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-title">Yearly & Monthly Focus</div>
                    </div>
                    <div class="card-body">
                        @if ($dashboard['yearlyDetail'])
                            <h5>Theme for the Year: {{ $dashboard['yearlyDetail']->year_theme }}</h5>
                            <p><em>{{ $dashboard['yearlyDetail']->year_scripture }}</em></p>
                            <hr>
                            <h5>Theme for {{ $dashboard['yearlyDetail']->current_month }}:
                                {{ $dashboard['yearlyDetail']->current_month_theme }}</h5>
                            <p><em>{{ $dashboard['yearlyDetail']->current_month_scripture }}</em></p>
                        @else
                            <p>Yearly details have not been set yet.</p>
                        @endif
                    </div>
                </div>
                <div class="card card-round">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">Latest Sermon</div>
                        <a href="{{ route('dashboard.sermons.index') }}" class="btn btn-link btn-sm">View All</a>
                    </div>
                    <div class="card-body">
                        @if ($dashboard['latestSermon'])
                            <h5>{{ $dashboard['latestSermon']->title }}</h5>
                            <p><strong>Preacher:</strong> {{ $dashboard['latestSermon']->speaker->name }}</p>
                            <a href="{{ route('sermons')}}" target="_blank" class="btn btn-primary btn-sm">Watch or Listen</a>
                        @else
                            <p>No sermons available at the moment.</p>
                        @endif
                    </div>
                </div>
                <div class="card card-round">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">Latest Gallery Additions</div>
                        <a href="{{ route('dashboard.media.index') }}" class="btn btn-link btn-sm">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($dashboard['latestMedia'] as $media)
                                <div class="col-md-3">
                                    <img src="{{ asset('storage/' . $media->file_path) }}" class="img-fluid rounded"
                                        alt="Gallery Image">
                                </div>
                            @empty
                                <p>No new media has been uploaded yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-title">Quick Links</div>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('dashboard.testimonies.create') }}" class="btn btn-primary btn-block mb-2">Submit Testimony</a>
                        <a href="{{ route('dashboard.announcements.create') }}" class="btn btn-secondary btn-block">Submit Announcement</a>
                    </div>
                </div>
                @if ($dashboard['latestAnnouncement'])
                    <div class="card card-round bg-warning text-white">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Announcement for Approval</h5>
                            <a href="{{ route('dashboard.announcements.index') }}" class="btn btn-link btn-sm text-white">View All</a>
                        </div>
                        <div class="card-body">
                            <p>{{ $dashboard['latestAnnouncement']->title }}</p>
                            <form
                                action="{{ route('dashboard.announcements.approve', $dashboard['latestAnnouncement']) }}"
                                method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                            </form>
                        </div>
                    </div>
                @endif
                <div class="card card-round">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">Testimonies for Approval</div>
                        <a href="{{ route('dashboard.testimonies.index') }}" class="btn btn-link btn-sm">View All</a>
                    </div>
                    <div class="card-body">
                        @forelse($dashboard['latestTestimonies'] as $testimony)
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p>"{{ Str::limit($testimony->content, 70) }}" - <strong>{{ $testimony->testifier_name }}</strong></p>
                                    <small class="text-muted">Status: {{ $testimony->is_approved ? 'Approved' : 'Pending' }}</small>
                                </div>
                                @if(!$testimony->is_approved)
                                <form action="{{ route('dashboard.testimonies.approve', $testimony) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                @endif
                            </div>
                            @if(!$loop->last) <hr> @endif
                        @empty
                            <p>No new testimonies are awaiting approval.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>