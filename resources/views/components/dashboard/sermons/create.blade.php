@include('components.dashboard.partials.form-shell')

<div class="container">
    <div class="page-inner">
        <div class="dashboard-form-shell">
            <div class="dashboard-form-hero card mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="dashboard-form-eyebrow">Sermons Form</span>
                            <h2 class="dashboard-form-title">Create a sermon entry with reusable media assets and a cleaner publishing workflow.</h2>
                            <p class="dashboard-form-subtitle">Choose sermon images, videos, and audio from the central media library, or upload a fresh cover image with an in-form 4:3 crop before saving.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="dashboard-form-hero-actions">
                                <a href="{{ route('dashboard.sermons.index') }}" class="btn btn-outline-secondary btn-lg dashboard-form-secondary-btn">Back to Sermons</a>
                                <div class="dashboard-form-note"><span class="dot"></span>Use the sermon media category in Media to prepare reusable assets</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card dashboard-form-card">
                <div class="card-header"><div class="card-title">Create Sermon</div></div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('dashboard.sermons.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <label class="form-label">Use Sermon Cover From Media Library</label>
                                <select name="cover_media_id" class="form-select">
                                    <option value="">Upload a new cover or leave empty</option>
                                    @foreach ($mediaLibrary['images'] as $item)
                                        <option value="{{ $item->id }}" {{ old('cover_media_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="dashboard-form-helper">Choose a centrally managed sermon image if one already exists in Media.</small>
                            </div>

                            <div class="col-lg-6 mb-4">
                                <div class="dashboard-form-preview-panel h-100">
                                    <p class="mb-2 fw-semibold">Sermon media workflow</p>
                                    <p class="dashboard-form-helper mb-0">Upload sermon assets once in Media under the <strong>Sermon</strong> category. Then reuse the image, YouTube video, or audio link here without duplicating uploads.</p>
                                </div>
                            </div>

                            <div class="col-md-12 mb-4">
                                <x-dashboard.partials.cropped-image-field
                                    label="Upload New Cover Image"
                                    source-name="cover_image_source"
                                    cropped-name="cover_image_cropped"
                                    source-id="sermon-cover-create"
                                    helper="Optional: upload a new sermon cover, drag to choose the exact focus area, then confirm the crop."
                                    empty-state="Select a sermon cover image to begin cropping."
                                    result-label="Final sermon cover"
                                />
                            </div>

                            <div class="col-md-6 mb-3"><label class="form-label">Title</label><input type="text" name="title" value="{{ old('title') }}" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Sermon Date</label><input type="date" name="sermon_date" value="{{ old('sermon_date') }}" class="form-control" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Speaker</label><select name="speaker_id" class="form-select" required><option value="">Select a speaker</option>@foreach ($speakers as $speaker)<option value="{{ $speaker->id }}" {{ old('speaker_id') == $speaker->id ? 'selected' : '' }}>{{ $speaker->name }}</option>@endforeach</select></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach ($statuses as $status)<option value="{{ $status->value }}" {{ old('status') == $status->value ? 'selected' : '' }}>{{ ucfirst($status->value) }}</option>@endforeach</select></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Duration (e.g., 45 mins)</label><input type="text" name="duration" value="{{ old('duration') }}" class="form-control"></div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Audio From Media Library</label>
                                <select name="audio_media_id" class="form-select">
                                    <option value="">Use external audio URL instead</option>
                                    @foreach ($mediaLibrary['audios'] as $item)
                                        <option value="{{ $item->id }}" {{ old('audio_media_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Video From Media Library</label>
                                <select name="video_media_id" class="form-select">
                                    <option value="">Use external video URL instead</option>
                                    @foreach ($mediaLibrary['videos'] as $item)
                                        <option value="{{ $item->id }}" {{ old('video_media_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->title }}{{ $item->youtube_video_url ? ' • YouTube ready' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3"><label class="form-label">Audio URL</label><input type="url" name="audio_url" value="{{ old('audio_url') }}" class="form-control"><small class="dashboard-form-helper">Optional fallback for an external audio link when no sermon media asset is selected.</small></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Video URL</label><input type="url" name="video_url" value="{{ old('video_url') }}" class="form-control"><small class="dashboard-form-helper">Optional fallback for an external video link when no sermon media asset is selected.</small></div>
                            <div class="col-md-12 mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea></div>
                            <div class="col-md-12 mb-3"><label class="form-label">Message</label><textarea name="message" class="form-control" rows="5">{{ old('message') }}</textarea></div>
                            <div class="col-md-12 mb-4">
                                <div class="dashboard-form-preview-panel h-100">
                                    <p class="mb-2 fw-semibold">SEO Settings</p>
                                    <p class="dashboard-form-helper mb-3">These fields are optional. Leave them empty to let the public sermon page auto-generate search metadata from the sermon content.</p>
                                    <div class="row">
                                        <div class="col-md-12 mb-3"><label class="form-label">Meta Title</label><input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-control" maxlength="255"></div>
                                        <div class="col-md-12 mb-3"><label class="form-label">Meta Description</label><textarea name="meta_description" class="form-control" rows="3" maxlength="320">{{ old('meta_description') }}</textarea></div>
                                        <div class="col-md-12 mb-0"><label class="form-label">Meta Keywords</label><input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" class="form-control" maxlength="255"><small class="dashboard-form-helper">Comma-separated keywords, such as church sermon, Bible teaching, Ajah Lagos.</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3"><label class="form-label">Attachments</label><input type="file" name="attachments[]" class="form-control" multiple></div>
                        </div>

                        <div class="dashboard-form-actions">
                            <button class="btn btn-primary dashboard-form-primary-btn">Create Sermon</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
