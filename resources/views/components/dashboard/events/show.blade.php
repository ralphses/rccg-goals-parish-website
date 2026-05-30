@php
    function getYoutubeId($url)
    {
        preg_match('/(youtube\.com\/(watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches);
        return $matches[3] ?? null;
    }
@endphp

<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Event Details</div>
                        <div>
                                @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                            <a href="{{ route('dashboard.events.edit', $event->id) }}" class="btn btn-primary">
                                Edit
                            </a>
                            @endif
                            <a href="{{ route('dashboard.events.index') }}" class="btn btn-light">
                                Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Image -->
                            @if ($event->image)
                                <div class="col-md-12 mb-4 text-center">
                                    <img src="{{ asset('storage/' . $event->image) }}" class="img-fluid rounded"
                                        style="max-height: 400px;">
                                </div>
                            @endif

                            <!-- Title -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Title</label>
                                <p class="form-control">{{ $event->title }}</p>
                            </div>

                            <!-- Date -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date</label>
                                <p class="form-control">{{ $event->event_date->format('d M, Y') }}</p>
                            </div>

                            <!-- Location -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location</label>
                                <p class="form-control">{{ $event->location ?? 'N/A' }}</p>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <p class="form-control">{{ ucfirst($event->status) }}</p>
                            </div>

                            <!-- Department -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department</label>
                                <p class="form-control">{{ $event->department->name ?? 'N/A' }}</p>
                            </div>

                            <!-- Description Heading -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Description Heading</label>
                                <p class="form-control">{{ $event->description_heading ?? 'N/A' }}</p>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <div class="form-control" style="height: auto;">{!! $event->description ?? 'N/A' !!}</div>
                            </div>

                            <!-- Video -->
                            @if ($event->video_link)
                                @php
                                    $youtubeId = getYoutubeId($event->video_link);
                                @endphp
                                @if ($youtubeId)
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Video</label>
                                        <div class="embed-responsive embed-responsive-16by9">
                                            <iframe class="embed-responsive-item"
                                                src="https://www.youtube.com/embed/{{ $youtubeId }}"
                                                allowfullscreen></iframe>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
