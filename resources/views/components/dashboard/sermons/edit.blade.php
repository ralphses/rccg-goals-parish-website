@props(['sermon', 'speakers', 'statuses'])

<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Edit Sermon</div>
                        <a href="{{ route('dashboard.sermons.index') }}" class="btn btn-light">Back</a>
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

                        <form method="POST" action="{{ route('dashboard.sermons.update', $sermon) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">

                                <!-- Image Preview -->
                                <div class="col-md-12 mb-3 text-center">
                                    <img id="image-preview" src="{{ $sermon->cover_image ? Storage::url($sermon->cover_image) : '' }}" width="200"
                                        class="img-fluid rounded mb-2" style="{{ $sermon->cover_image ? '' : 'display: none;' }}">
                                </div>

                                <!-- Cover Image -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Cover Image</label>
                                    <input type="file" name="cover_image" id="image-input" class="form-control">
                                </div>

                                <!-- Title -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" value="{{ old('title', $sermon->title) }}"
                                        class="form-control" required>
                                </div>

                                <!-- Sermon Date -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sermon Date</label>
                                    <input type="date" name="sermon_date"
                                        value="{{ old('sermon_date', $sermon->sermon_date->format('Y-m-d')) }}" class="form-control" required>
                                </div>

                                <!-- Speaker -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Speaker</label>
                                    <select name="speaker_id" class="form-select" required>
                                        <option value="">Select a speaker</option>
                                        @foreach ($speakers as $speaker)
                                            <option value="{{ $speaker->id }}"
                                                {{ old('speaker_id', $sermon->speaker_id) == $speaker->id ? 'selected' : '' }}>
                                                {{ $speaker->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->value }}"
                                                {{ old('status', $sermon->status) == $status->value ? 'selected' : '' }}>
                                                {{ ucfirst($status->value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Duration -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Duration (e.g., 45 mins)</label>
                                    <input type="text" name="duration" value="{{ old('duration', $sermon->duration) }}"
                                        class="form-control">
                                </div>

                                <!-- Audio URL -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Audio URL</label>
                                    <input type="url" name="audio_url" value="{{ old('audio_url', $sermon->audio_url) }}"
                                        class="form-control">
                                </div>

                                <!-- Video URL -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Video URL</label>
                                    <input type="url" name="video_url" value="{{ old('video_url', $sermon->video_url) }}"
                                        class="form-control">
                                </div>

                                <!-- Description -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control"
                                        rows="3">{{ old('description', $sermon->description) }}</textarea>
                                </div>

                                <!-- Message -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Message</label>
                                    <textarea name="message" class="form-control"
                                        rows="5">{{ old('message', $sermon->message) }}</textarea>
                                </div>

                                <!-- Attachments -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Current Attachments</label>
                                    @if($sermon->attachments->count() > 0)
                                        <ul class="list-group">
                                            @foreach($sermon->attachments as $attachment)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <a href="{{ route('dashboard.sermon-attachments.download', $attachment) }}">{{ $attachment->file_name }}</a>
                                                    <a href="#" class="btn btn-danger btn-sm" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this attachment?')) { document.getElementById('delete-attachment-{{ $attachment->id }}').submit(); }">Delete</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="form-text">No attachments for this sermon.</p>
                                    @endif
                                </div>

                                <!-- New Attachments -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Upload New Attachments</label>
                                    <input type="file" name="attachments[]" class="form-control" multiple>
                                </div>

                            </div>

                            <hr>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Update Sermon</button>
                            </div>

                        </form>

                        <!-- Hidden Deletion Forms -->
                        @if($sermon->attachments->count() > 0)
                            @foreach($sermon->attachments as $attachment)
                                <form id="delete-attachment-{{ $attachment->id }}" action="{{ route('dashboard.sermon-attachments.destroy', $attachment) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endforeach
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const imageInput = document.getElementById('image-input');
        if (imageInput) {
            imageInput.addEventListener('change', function(event) {
                const [file] = event.target.files;
                if (file) {
                    const preview = document.getElementById('image-preview');
                    if (preview) {
                        preview.src = URL.createObjectURL(file);
                        preview.style.display = 'block';
                    }
                }
            });
        }
    });
</script>