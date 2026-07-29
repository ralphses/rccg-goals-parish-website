@props(['sermon', 'speakers', 'statuses', 'mediaLibrary'])
@include('components.dashboard.partials.form-shell')

<div class="container">
    <div class="page-inner">
        <div class="dashboard-form-shell">
            <div class="dashboard-form-hero card mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="dashboard-form-eyebrow">Sermons Form</span>
                            <h2 class="dashboard-form-title">Edit this sermon with reusable media assets, message updates, and attachments in one place.</h2>
                            <p class="dashboard-form-subtitle">Keep the current sermon cover, linked media, and attachments visible while you refresh the message and publishing details.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="dashboard-form-hero-actions">
                                <a href="{{ route('dashboard.sermons.index') }}" class="btn btn-outline-secondary btn-lg dashboard-form-secondary-btn">Back to Sermons</a>
                                <div class="dashboard-form-note"><span class="dot"></span>Shared media assets stay reusable across the application</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card dashboard-form-card">
                <div class="card-header"><div class="card-title">Edit Sermon</div></div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('dashboard.sermons.update', $sermon) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <label class="form-label">Use Sermon Cover From Media Library</label>
                                <select name="cover_media_id" class="form-select">
                                    <option value="">Keep current custom cover or upload a new one</option>
                                    @foreach ($mediaLibrary['images'] as $item)
                                        <option value="{{ $item->id }}" {{ old('cover_media_id', $sermon->cover_media_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="dashboard-form-helper">Selecting a sermon media image makes this record reuse the centralized media asset.</small>
                            </div>

                            <div class="col-lg-6 mb-4">
                                <div class="dashboard-form-preview-panel h-100">
                                    <p class="mb-2 fw-semibold">Current cover source</p>
                                    <p class="dashboard-form-helper mb-0">
                                        {{ $sermon->cover_media_id ? 'This sermon currently uses a shared media-library cover image.' : 'This sermon currently uses its own stored cover image.' }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-12 mb-4">
                                <x-dashboard.partials.cropped-image-field
                                    label="Upload New Cover Image"
                                    source-name="cover_image_source"
                                    cropped-name="cover_image_cropped"
                                    source-id="sermon-cover-edit"
                                    :current-url="$sermon->cover_image_url"
                                    current-label="Current cover image"
                                    helper="Optional: upload a fresh sermon cover, drag to choose the best section, then confirm the crop."
                                    empty-state="Select a new sermon cover image to begin cropping."
                                    result-label="Final sermon cover"
                                />
                            </div>

                            <div class="col-md-6 mb-3"><label class="form-label">Title</label><input type="text" name="title" value="{{ old('title', $sermon->title) }}" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Sermon Date</label><input type="date" name="sermon_date" value="{{ old('sermon_date', $sermon->sermon_date->format('Y-m-d')) }}" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Speaker</label><select name="speaker_id" class="form-select" required><option value="">Select a speaker</option>@foreach ($speakers as $speaker)<option value="{{ $speaker->id }}" {{ old('speaker_id', $sermon->speaker_id) == $speaker->id ? 'selected' : '' }}>{{ $speaker->name }}</option>@endforeach</select></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach ($statuses as $status)<option value="{{ $status->value }}" {{ old('status', $sermon->status) == $status->value ? 'selected' : '' }}>{{ ucfirst($status->value) }}</option>@endforeach</select></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Duration (e.g., 45 mins)</label><input type="text" name="duration" value="{{ old('duration', $sermon->duration) }}" class="form-control"></div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Audio From Media Library</label>
                                <select name="audio_media_id" class="form-select">
                                    <option value="">Keep or use external audio URL</option>
                                    @foreach ($mediaLibrary['audios'] as $item)
                                        <option value="{{ $item->id }}" {{ old('audio_media_id', $sermon->audio_media_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Video From Media Library</label>
                                <select name="video_media_id" class="form-select">
                                    <option value="">Keep or use external video URL</option>
                                    @foreach ($mediaLibrary['videos'] as $item)
                                        <option value="{{ $item->id }}" {{ old('video_media_id', $sermon->video_media_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->title }}{{ $item->youtube_video_url ? ' • YouTube ready' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3"><label class="form-label">Audio URL</label><input type="url" name="audio_url" value="{{ old('audio_url', $sermon->audio_url) }}" class="form-control"><small class="dashboard-form-helper">Optional fallback when no shared sermon audio is selected.</small></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Video URL</label><input type="url" name="video_url" value="{{ old('video_url', $sermon->video_url) }}" class="form-control"><small class="dashboard-form-helper">Optional fallback when no shared sermon video is selected.</small></div>
                            <div class="col-md-12 mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3">{{ old('description', $sermon->description) }}</textarea></div>
                            <div class="col-md-12 mb-3"><label class="form-label">Message</label><textarea name="message" class="form-control" rows="5">{{ old('message', $sermon->message) }}</textarea></div>
                            <div class="col-md-12 mb-4">
                                <div class="dashboard-form-preview-panel h-100">
                                    <p class="mb-2 fw-semibold">SEO Settings</p>
                                    <p class="dashboard-form-helper mb-3">Optional overrides for the public sermon page. Leave blank to keep using generated metadata from the sermon itself.</p>
                                    <div class="row">
                                        <div class="col-md-12 mb-3"><label class="form-label">Meta Title</label><input type="text" name="meta_title" value="{{ old('meta_title', $sermon->meta_title) }}" class="form-control" maxlength="255"></div>
                                        <div class="col-md-12 mb-3"><label class="form-label">Meta Description</label><textarea name="meta_description" class="form-control" rows="3" maxlength="320">{{ old('meta_description', $sermon->meta_description) }}</textarea></div>
                                        <div class="col-md-12 mb-0"><label class="form-label">Meta Keywords</label><input type="text" name="meta_keywords" value="{{ old('meta_keywords', $sermon->meta_keywords) }}" class="form-control" maxlength="255"></div>
                                    </div>
                                </div>
                            </div>

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
                                    <p class="dashboard-form-helper mb-0">No attachments for this sermon.</p>
                                @endif
                            </div>

                            <div class="col-md-12 mb-3"><label class="form-label">Upload New Attachments</label><input type="file" name="attachments[]" class="form-control" multiple></div>
                        </div>

                        <div class="dashboard-form-actions">
                            <button type="submit" class="btn btn-primary dashboard-form-primary-btn">Update Sermon</button>
                        </div>
                    </form>

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
