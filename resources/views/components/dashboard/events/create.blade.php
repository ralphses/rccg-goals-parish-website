@props(['departments', 'statuses', 'mediaLibrary'])
@include('components.dashboard.partials.form-shell')

<div class="container">
    <div class="page-inner">
        <div class="dashboard-form-shell">
            <div class="dashboard-form-hero card mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="dashboard-form-eyebrow">Events Form</span>
                            <h2 class="dashboard-form-title">Create a new event with better structure and clearer scheduling fields.</h2>
                            <p class="dashboard-form-subtitle">Add the event image, reusable event video, timing, department ownership, and description in one focused workflow.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="dashboard-form-hero-actions">
                                <a href="{{ route('dashboard.events.index') }}" class="btn btn-outline-secondary btn-lg dashboard-form-secondary-btn">Back to Events</a>
                                <div class="dashboard-form-note"><span class="dot"></span>Upload once or reuse media already prepared in the library</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card dashboard-form-card">
                <div class="card-header"><div class="card-title">Create Event</div></div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('dashboard.events.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <label class="form-label">Use Event Image From Media Library</label>
                                <select name="image_media_id" class="form-select">
                                    <option value="">Upload a new image or leave empty</option>
                                    @foreach ($mediaLibrary['images'] as $item)
                                        <option value="{{ $item->id }}" {{ old('image_media_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="dashboard-form-helper">Choose a reusable event image from Media if one already exists.</small>
                            </div>
                            <div class="col-lg-6 mb-4">
                                <div class="dashboard-form-preview-panel h-100">
                                    <p class="mb-2 fw-semibold">Event media workflow</p>
                                    <p class="dashboard-form-helper mb-0">Images uploaded here are saved into the shared Media library under the <strong>Event</strong> category so they can be reused later.</p>
                                </div>
                            </div>
                            <div class="col-md-12 mb-4">
                                <x-dashboard.partials.cropped-image-field
                                    label="Upload New Event Image"
                                    source-name="image_source"
                                    cropped-name="image_cropped"
                                    source-id="event-image-create"
                                    helper="Optional: upload a new event image, drag to choose the strongest section, then confirm the crop."
                                    empty-state="Select an event image to begin cropping."
                                    result-label="Final event image"
                                />
                            </div>
                            <div class="col-md-6 mb-3"><label class="form-label">Title</label><input type="text" name="title" value="{{ old('title') }}" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Event Date</label><input type="datetime-local" name="event_date" value="{{ old('event_date') }}" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Location</label><input type="text" name="location" value="{{ old('location') }}" class="form-control"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach ($statuses as $status)<option value="{{ $status->value }}" {{ old('status') == $status->value ? 'selected' : '' }}>{{ ucfirst($status->value) }}</option>@endforeach</select></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Department</label><select name="department_id" class="form-select"><option value="">Select a department</option>@foreach ($departments as $department)<option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>@endforeach</select></div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Event Video From Media Library</label>
                                <select name="video_media_id" class="form-select">
                                    <option value="">Use external video URL instead</option>
                                    @foreach ($mediaLibrary['videos'] as $item)
                                        <option value="{{ $item->id }}" {{ old('video_media_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->title }}{{ $item->youtube_video_url ? ' • YouTube ready' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3"><label class="form-label">Video Link</label><input type="url" name="video_link" value="{{ old('video_link') }}" class="form-control"><small class="dashboard-form-helper">Optional fallback when no shared event video is selected.</small></div>
                            <div class="col-md-12 mb-3"><label class="form-label">Description Heading</label><input type="text" name="description_heading" value="{{ old('description_heading') }}" class="form-control"></div>
                            <div class="col-md-12 mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="5">{{ old('description') }}</textarea></div>
                            <div class="col-md-12 mb-4">
                                <div class="dashboard-form-preview-panel h-100">
                                    <p class="mb-2 fw-semibold">SEO Settings</p>
                                    <p class="dashboard-form-helper mb-3">Optional search metadata for the public event page.</p>
                                    <div class="row">
                                        <div class="col-md-12 mb-3"><label class="form-label">Meta Title</label><input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-control" maxlength="255"></div>
                                        <div class="col-md-12 mb-3"><label class="form-label">Meta Description</label><textarea name="meta_description" class="form-control" rows="3" maxlength="320">{{ old('meta_description') }}</textarea></div>
                                        <div class="col-md-12 mb-0"><label class="form-label">Meta Keywords</label><input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" class="form-control" maxlength="255"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-form-actions">
                            <button class="btn btn-primary dashboard-form-primary-btn">Create Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
