<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Create Event</div>
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

                        <form method="POST" action="{{ route('dashboard.events.store') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                <!-- Image Preview -->
                                <div class="col-md-12 mb-3 text-center">
                                    <img id="image-preview" src="" width="200"
                                        class="img-fluid rounded mb-2" style="display: none;">
                                </div>

                                <!-- Image -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Image</label>
                                    <input type="file" name="image" id="image-input" class="form-control">
                                </div>

                                <!-- Title -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" value="{{ old('title') }}"
                                        class="form-control" required>
                                </div>

                                <!-- Event Date -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Event Date</label>
                                    <input type="datetime-local" name="event_date"
                                        value="{{ old('event_date') }}" class="form-control" required>
                                </div>

                                <!-- Location -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="location" value="{{ old('location') }}"
                                        class="form-control">
                                </div>

                                <!-- Status -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->value }}"
                                                {{ old('status') == $status->value ? 'selected' : '' }}>
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
                                                {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Video Link -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Video Link</label>
                                    <input type="url" name="video_link" value="{{ old('video_link') }}"
                                        class="form-control">
                                </div>

                                <!-- Description Heading -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description Heading</label>
                                    <input type="text" name="description_heading"
                                        value="{{ old('description_heading') }}" class="form-control">
                                </div>

                                <!-- Description -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control"
                                        rows="5">{{ old('description') }}</textarea>
                                </div>

                            </div>

                            <hr>

                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary">Create Event</button>
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