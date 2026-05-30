<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Edit Event</div>
                        <a href="{{ route('dashboard.events.index') }}" class="btn btn-light">Back</a>
                    </div>

                    <div class="card-body">

                        <!-- Validation Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('dashboard.events.update', $event->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">

                                <!-- Current Image Preview -->
                                <div class="col-md-12 mb-3 text-center">
                                    <img id="image-preview"
                                        src="{{ $event->image ? asset('storage/' . $event->image) : '' }}"
                                        width="200" class="img-fluid rounded mb-2"
                                        style="{{ $event->image ? '' : 'display: none;' }}">
                                </div>

                                <!-- Upload New Image -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Upload New Image</label>
                                    <input type="file" name="image" id="image-input" class="form-control">
                                    <small class="text-muted">Optional: Upload a new image for the event</small>
                                </div>

                                <!-- Title -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" value="{{ old('title', $event->title) }}"
                                        class="form-control" required>
                                </div>

                                <!-- Event Date -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Event Date</label>
                                    <input type="datetime-local" name="event_date"
                                        value="{{ old('event_date', $event->event_date->format('Y-m-d\TH:i')) }}"
                                        class="form-control" required>
                                </div>

                                <!-- Location -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="location"
                                        value="{{ old('location', $event->location) }}" class="form-control">
                                </div>

                                <!-- Status -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}"
                                                {{ $event->status == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status->value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Department -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Department</label>
                                    <select name="department_id" class="form-select">
                                        <option value="">Select a department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ $event->department_id == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Video Link -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Video Link</label>
                                    <input type="url" name="video_link"
                                        value="{{ old('video_link', $event->video_link) }}" class="form-control">
                                </div>

                                <!-- Description Heading -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description Heading</label>
                                    <input type="text" name="description_heading"
                                        value="{{ old('description_heading', $event->description_heading) }}"
                                        class="form-control">
                                </div>

                                <!-- Description -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control"
                                        rows="5">{{ old('description', $event->description) }}</textarea>
                                </div>

                            </div>

                            <hr>

                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary">Update Event</button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('image-input').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const preview = document.getElementById('image-preview');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });
</script>