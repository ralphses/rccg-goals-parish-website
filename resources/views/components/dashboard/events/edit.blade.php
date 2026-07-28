@include('components.dashboard.partials.form-shell')

<div class="container">
    <div class="page-inner">
        <div class="dashboard-form-shell">
            <div class="dashboard-form-hero card mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="dashboard-form-eyebrow">Events Form</span>
                            <h2 class="dashboard-form-title">Update this event with better visibility into the image, schedule, and status.</h2>
                            <p class="dashboard-form-subtitle">Refresh event details confidently while keeping the current media and timing context in view.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="dashboard-form-hero-actions">
                                <a href="{{ route('dashboard.events.index') }}" class="btn btn-outline-secondary btn-lg dashboard-form-secondary-btn">Back to Events</a>
                                <div class="dashboard-form-note"><span class="dot"></span>Current image remains visible until you replace it</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card dashboard-form-card">
                <div class="card-header"><div class="card-title">Edit Event</div></div>
                <div class="card-body">
                    @if ($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
                    <form method="POST" action="{{ route('dashboard.events.update', $event->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <x-dashboard.partials.cropped-image-field
                                    label="Upload New Image"
                                    source-name="image_source"
                                    cropped-name="image_cropped"
                                    source-id="event-image-edit"
                                    :current-url="$event->image_url"
                                    current-label="Current event image"
                                    helper="Optional: replace the event image, drag to reposition the subject, then confirm the crop."
                                    empty-state="Select a new event image to begin cropping."
                                    result-label="Final event image"
                                />
                            </div>
                            <div class="col-md-6 mb-3"><label class="form-label">Title</label><input type="text" name="title" value="{{ old('title', $event->title) }}" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Event Date</label><input type="datetime-local" name="event_date" value="{{ old('event_date', $event->event_date->format('Y-m-d\TH:i')) }}" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Location</label><input type="text" name="location" value="{{ old('location', $event->location) }}" class="form-control"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach ($statuses as $status)<option value="{{ $status->value }}" {{ old('status', $event->status?->value ?? $event->status) == $status->value ? 'selected' : '' }}>{{ ucfirst($status->value) }}</option>@endforeach</select></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Department</label><select name="department_id" class="form-select"><option value="">Select a department</option>@foreach ($departments as $department)<option value="{{ $department->id }}" {{ $event->department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>@endforeach</select></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Video Link</label><input type="url" name="video_link" value="{{ old('video_link', $event->video_link) }}" class="form-control"></div>
                            <div class="col-md-12 mb-3"><label class="form-label">Description Heading</label><input type="text" name="description_heading" value="{{ old('description_heading', $event->description_heading) }}" class="form-control"></div>
                            <div class="col-md-12 mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="5">{{ old('description', $event->description) }}</textarea></div>
                        </div>
                        <div class="dashboard-form-actions"><button class="btn btn-primary dashboard-form-primary-btn">Update Event</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
