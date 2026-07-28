@props([
    'categories',
    'media' => null,
    'youtubeConnected' => false,
])

@php
    $isEdit = $media !== null;
    $selectedType = old('media_type', $media?->media_type?->value ?? 'image');
    $selectedCategory = old('category', $media?->category?->value);
    $title = old('title', $media?->title);
    $isPublic = old('is_public', $media?->is_public ?? true);
    $submitLabel = $isEdit ? 'Update Media' : 'Upload Media';
    $heading = $isEdit ? 'Edit Media' : 'Upload Media';
    $cardTitle = $isEdit ? 'Refine Media Asset' : 'Create Media Asset';
    $existingVideoSrc = $isEdit && $media->media_type->value === 'video' ? $media->file_url : '';
    $existingAudioSrc = $isEdit && $media->media_type->value === 'audio' ? $media->file_url : '';
    $youtubePublish = old('publish_to_youtube', $isEdit && $media?->publish_to_youtube ? '1' : '0') === '1';
    $youtubeFormat = old('youtube_format', $media?->youtube_format?->value ?? 'full_video');
    $youtubeTitle = old('youtube_title', $media?->youtube_title ?? $title);
    $youtubeDescription = old('youtube_description', $media?->youtube_description);
    $existingYouTubeStatus = $isEdit ? $media?->youtube_status?->value : null;
@endphp

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">{{ $heading }}</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="{{ route('dashboard') }}">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="{{ route('dashboard.media.index') }}">Media</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">{{ $isEdit ? 'Edit' : 'Upload' }}</a>
                </li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card media-upload-shell">
                    <div class="card-header media-upload-header">
                        <div>
                            <div class="media-upload-eyebrow">{{ $isEdit ? 'Update With Confidence' : 'Modern Upload Flow' }}</div>
                            <div class="card-title mb-1">{{ $cardTitle }}</div>
                            <p class="media-upload-subtitle mb-0">
                                A cleaner workflow for uploading images, videos, and audio with consistent presentation across the church site.
                            </p>
                        </div>
                        <div class="media-upload-badges">
                            <span class="media-upload-badge"><i class="fas fa-crop-alt"></i> 4:3 crop</span>
                            <span class="media-upload-badge"><i class="fas fa-expand-arrows-alt"></i> 1600 x 1200</span>
                            <span class="media-upload-badge"><i class="fab fa-youtube"></i> Private-first publish</span>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form
                            action="{{ $isEdit ? route('dashboard.media.update', $media->id) : route('dashboard.media.store') }}"
                            method="POST"
                            enctype="multipart/form-data"
                            data-media-form
                            data-youtube-connected="{{ $youtubeConnected ? 'true' : 'false' }}"
                        >
                            @csrf
                            @if ($isEdit)
                                @method('PUT')
                            @endif

                            <div class="media-quick-guide">
                                <div class="guide-step">
                                    <span class="guide-step-number">1</span>
                                    <div>
                                        <h6>Describe It</h6>
                                        <p>Give the media a title, category, and type.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <span class="guide-step-number">2</span>
                                    <div>
                                        <h6>Prepare It</h6>
                                        <p>Upload the source file and crop when needed.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <span class="guide-step-number">3</span>
                                    <div>
                                        <h6>Publish It</h6>
                                        <p>Save the app asset and optionally queue YouTube publishing.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="media-section-card mb-4">
                                <div class="media-section-head">
                                    <div>
                                        <h5 class="mb-1">Media Details</h5>
                                        <p class="mb-0">Start with the information used to identify and group this upload.</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ $title }}" required placeholder="Enter media title" data-main-title>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="category">Category</label>
                                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                                <option value="" disabled {{ $selectedCategory ? '' : 'selected' }}>Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->value }}" {{ $selectedCategory === $category->value ? 'selected' : '' }}>
                                                        {{ ucwords(str_replace('_', ' ', $category->value)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="media_type">Media Type</label>
                                            <select class="form-select @error('media_type') is-invalid @enderror" id="media_type" name="media_type" required data-media-type-select>
                                                <option value="image" {{ $selectedType === 'image' ? 'selected' : '' }}>Image</option>
                                                <option value="video" {{ $selectedType === 'video' ? 'selected' : '' }}>Video</option>
                                                <option value="audio" {{ $selectedType === 'audio' ? 'selected' : '' }}>Audio</option>
                                            </select>
                                            @error('media_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="media-guidance-banner mb-4">
                                <div class="guidance-icon">
                                    <i class="fas fa-magic"></i>
                                </div>
                                <div>
                                    <strong>Upload rule</strong>
                                    <p class="mb-0">
                                        Visual uploads use a `4:3` crop and are stored as the final uploaded asset at `1600 x 1200`.
                                        Images are cropped before upload. Videos keep the uploaded file and require a cropped thumbnail.
                                        Audio uploads do not use cropping.
                                    </p>
                                </div>
                            </div>

                            <div class="media-type-switcher mb-4">
                                <button type="button" class="media-type-chip {{ $selectedType === 'image' ? 'is-active' : '' }}" data-type-chip="image">
                                    <i class="fas fa-image"></i>
                                    <span>Image</span>
                                    <small>Crop and store the final image</small>
                                </button>
                                <button type="button" class="media-type-chip {{ $selectedType === 'video' ? 'is-active' : '' }}" data-type-chip="video">
                                    <i class="fas fa-video"></i>
                                    <span>Video</span>
                                    <small>Upload video, crop thumbnail, optionally publish to YouTube</small>
                                </button>
                                <button type="button" class="media-type-chip {{ $selectedType === 'audio' ? 'is-active' : '' }}" data-type-chip="audio">
                                    <i class="fas fa-music"></i>
                                    <span>Audio</span>
                                    <small>Upload directly with no crop step</small>
                                </button>
                            </div>

                            <div class="media-type-panel {{ $selectedType === 'image' ? '' : 'd-none' }}" data-media-panel="image">
                                <div class="media-section-card">
                                    <div class="media-section-head">
                                        <div>
                                            <h5 class="mb-1">Image Upload</h5>
                                            <p class="mb-0">Choose an image, drag it within the frame, and save the final cropped result.</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="source_image">Source Image</label>
                                                <input type="file" class="form-control @error('source_image') is-invalid @enderror" id="source_image" name="source_image" accept="image/jpeg,image/png,image/webp" data-source-input="image" data-panel-input>
                                                <small class="text-muted">Choose a source image, then crop it below. The cropped result becomes the stored upload.</small>
                                                @error('source_image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <input type="hidden" name="cropped_image" value="{{ old('cropped_image') }}" data-cropped-output="image" data-panel-input>
                                            @error('cropped_image')
                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                            @enderror

                                            @if ($isEdit && $media->media_type->value === 'image')
                                                <div class="media-existing-preview mt-3">
                                                    <p class="mb-2 fw-semibold">Current stored image</p>
                                                    <img src="{{ $media->visual_url }}" alt="{{ $media->title }}" class="img-fluid rounded border">
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="cropper-card" data-cropper="image" data-has-existing="{{ $isEdit && $media->media_type->value === 'image' ? 'true' : 'false' }}">
                                                <div class="cropper-state text-muted" data-crop-status="image">Select a source image to begin cropping.</div>
                                                <div class="crop-frame d-none" data-crop-frame="image">
                                                    <img alt="Image crop preview" data-crop-image="image">
                                                    <div class="crop-overlay">
                                                        <span>Drag image to choose the section to keep</span>
                                                    </div>
                                                </div>
                                                <div class="mt-3 d-none" data-crop-controls="image">
                                                    <label class="form-label" for="zoom_image">Zoom</label>
                                                    <input type="range" class="form-range" min="1" max="3" step="0.01" value="1" id="zoom_image" data-crop-zoom="image">
                                                    <div class="d-flex gap-2 mt-3">
                                                        <button type="button" class="btn btn-primary btn-sm" data-crop-confirm="image">Use Cropped Image</button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-crop-reset="image">Reset</button>
                                                    </div>
                                                </div>
                                                <div class="crop-result mt-3 d-none" data-crop-result-wrapper="image">
                                                    <p class="mb-2 fw-semibold">Final uploaded image</p>
                                                    <img alt="Final cropped image" class="img-fluid rounded border" data-crop-result="image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="media-type-panel {{ $selectedType === 'video' ? '' : 'd-none' }}" data-media-panel="video">
                                <div class="media-section-card">
                                    <div class="media-section-head">
                                        <div>
                                            <h5 class="mb-1">Video Upload</h5>
                                            <p class="mb-0">Upload the video file, crop the thumbnail, and optionally queue it for publishing to the church YouTube channel.</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="file">Video File</label>
                                                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept="video/mp4,video/quicktime,video/ogg,video/webm" data-video-input data-panel-input>
                                                <small class="text-muted">Upload the video file. The video itself is not cropped, and the public app copy uploads in the background after you save.</small>
                                                @error('file')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <video class="img-fluid rounded border mt-3 {{ $isEdit && $media->media_type->value === 'video' ? '' : 'd-none' }}" controls data-video-preview src="{{ $existingVideoSrc }}"></video>
                                            <input type="hidden" name="video_duration_seconds" value="{{ old('video_duration_seconds') }}" data-video-duration data-panel-input>
                                            <input type="hidden" name="video_width" value="{{ old('video_width') }}" data-video-width data-panel-input>
                                            <input type="hidden" name="video_height" value="{{ old('video_height') }}" data-video-height data-panel-input>
                                            <div class="video-meta-note mt-3 text-muted small" data-video-meta-status>
                                                Video metadata will be detected here to support Shorts validation.
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="thumbnail_source_image">Video Thumbnail Source Image</label>
                                                <input type="file" class="form-control @error('thumbnail_source_image') is-invalid @enderror" id="thumbnail_source_image" name="thumbnail_source_image" accept="image/jpeg,image/png,image/webp" data-source-input="video" data-panel-input>
                                                <small class="text-muted">Crop the thumbnail below. The cropped thumbnail is what appears in listings and galleries.</small>
                                                @error('thumbnail_source_image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <input type="hidden" name="cropped_thumbnail" value="{{ old('cropped_thumbnail') }}" data-cropped-output="video" data-panel-input>
                                            @error('cropped_thumbnail')
                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                            @enderror

                                            @if ($isEdit && $media->media_type->value === 'video' && $media->thumbnail_path)
                                                <div class="media-existing-preview mt-3">
                                                    <p class="mb-2 fw-semibold">Current stored thumbnail</p>
                                                    <img src="{{ $media->visual_url }}" alt="{{ $media->title }}" class="img-fluid rounded border">
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-lg-8">
                                            <div class="cropper-card" data-cropper="video" data-has-existing="{{ $isEdit && $media->media_type->value === 'video' && $media->thumbnail_path ? 'true' : 'false' }}">
                                                <div class="cropper-state text-muted" data-crop-status="video">Select a thumbnail source image to begin cropping.</div>
                                                <div class="crop-frame d-none" data-crop-frame="video">
                                                    <img alt="Video thumbnail crop preview" data-crop-image="video">
                                                    <div class="crop-overlay">
                                                        <span>Drag image to choose the section to keep</span>
                                                    </div>
                                                </div>
                                                <div class="mt-3 d-none" data-crop-controls="video">
                                                    <label class="form-label" for="zoom_video">Zoom</label>
                                                    <input type="range" class="form-range" min="1" max="3" step="0.01" value="1" id="zoom_video" data-crop-zoom="video">
                                                    <div class="d-flex gap-2 mt-3">
                                                        <button type="button" class="btn btn-primary btn-sm" data-crop-confirm="video">Use Cropped Thumbnail</button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-crop-reset="video">Reset</button>
                                                    </div>
                                                </div>
                                                <div class="crop-result mt-3 d-none" data-crop-result-wrapper="video">
                                                    <p class="mb-2 fw-semibold">Final uploaded thumbnail</p>
                                                    <img alt="Final cropped video thumbnail" class="img-fluid rounded border" data-crop-result="video">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="you-tube-panel mt-4">
                                        <div class="you-tube-panel__head">
                                            <div>
                                                <h5 class="mb-1">YouTube Publishing</h5>
                                                <p class="mb-0 text-muted">Queue this video for the shared church YouTube channel after the app finishes the background upload successfully.</p>
                                            </div>
                                            @if ($youtubeConnected)
                                                <span class="badge bg-success">Channel Connected</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Channel Not Connected</span>
                                            @endif
                                        </div>

                                        @if (!$youtubeConnected)
                                            <div class="alert alert-warning mb-0">
                                                Connect the church YouTube channel in <a href="{{ route('settings.index') }}">settings</a> before enabling YouTube publishing.
                                            </div>
                                        @else
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-check form-switch mt-1">
                                                        <input class="form-check-input" type="checkbox" role="switch" value="1" id="publish_to_youtube" name="publish_to_youtube" {{ $youtubePublish ? 'checked' : '' }} data-youtube-toggle data-panel-input>
                                                        <label class="form-check-label fw-semibold" for="publish_to_youtube">Publish this video to YouTube</label>
                                                    </div>
                                                    <small class="text-muted">Uploads go to YouTube in the background and default to private visibility.</small>
                                                    @error('publish_to_youtube')
                                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="shorts-guidance">
                                                        <strong>Shorts rule</strong>
                                                        <p class="mb-0 small text-muted">Shorts must be user-supplied vertical or square videos that are 3 minutes or less. The app will not generate Shorts automatically in this phase.</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3 {{ $youtubePublish ? '' : 'd-none' }}" data-youtube-fields>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="youtube_format">YouTube Format</label>
                                                        <select class="form-select @error('youtube_format') is-invalid @enderror" id="youtube_format" name="youtube_format" data-youtube-format data-panel-input>
                                                            <option value="full_video" {{ $youtubeFormat === 'full_video' ? 'selected' : '' }}>Full Video</option>
                                                            <option value="short" {{ $youtubeFormat === 'short' ? 'selected' : '' }}>Short</option>
                                                        </select>
                                                        @error('youtube_format')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <label for="youtube_title">YouTube Title</label>
                                                        <input type="text" class="form-control @error('youtube_title') is-invalid @enderror" id="youtube_title" name="youtube_title" value="{{ $youtubeTitle }}" maxlength="100" data-youtube-title data-panel-input>
                                                        @error('youtube_title')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group mb-0">
                                                        <label for="youtube_description">YouTube Description</label>
                                                        <textarea class="form-control @error('youtube_description') is-invalid @enderror" id="youtube_description" name="youtube_description" rows="4" maxlength="5000" data-panel-input>{{ $youtubeDescription }}</textarea>
                                                        @error('youtube_description')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <div class="video-meta-box" data-shorts-check>
                                                        <div class="fw-semibold mb-1">Shorts readiness</div>
                                                        <div class="small text-muted" data-shorts-status>
                                                            Choose a video file so the app can confirm dimensions and duration for Shorts.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($isEdit && $media->media_type->value === 'video' && $media->publish_to_youtube)
                                            <div class="current-youtube-status mt-3">
                                                <strong>Current YouTube status:</strong>
                                                <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $existingYouTubeStatus ?? 'not_requested')) }}</span>
                                                @if ($media->youtube_video_url)
                                                    <a href="{{ $media->youtube_video_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-danger ms-2">Open on YouTube</a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="media-type-panel {{ $selectedType === 'audio' ? '' : 'd-none' }}" data-media-panel="audio">
                                <div class="media-section-card">
                                    <div class="media-section-head">
                                        <div>
                                            <h5 class="mb-1">Audio Upload</h5>
                                            <p class="mb-0">Upload the audio file directly. No crop step is required for this media type.</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="audio_file">Audio File</label>
                                                <input type="file" class="form-control @error('file') is-invalid @enderror" id="audio_file" name="file" accept="audio/mpeg,audio/wav,audio/x-wav,audio/ogg" data-audio-input data-panel-input>
                                                <small class="text-muted">Audio uploads do not need cropping.</small>
                                                @error('file')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <audio class="w-100 mt-3 {{ $isEdit && $media->media_type->value === 'audio' ? '' : 'd-none' }}" controls data-audio-preview src="{{ $existingAudioSrc }}"></audio>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="media-section-card mt-4">
                                <div class="media-section-head">
                                    <div>
                                        <h5 class="mb-1">Visibility and Save</h5>
                                        <p class="mb-0">Choose whether this item should be public, then save the upload.</p>
                                    </div>
                                </div>

                                <div class="media-upload-progress d-none mb-4" data-upload-progress>
                                    <div class="media-upload-progress__head">
                                        <div>
                                            <strong data-upload-progress-label>Preparing upload...</strong>
                                            <p class="mb-0 text-muted small">Please keep this page open while your file is uploading.</p>
                                        </div>
                                        <span class="media-upload-progress__percent" data-upload-progress-percent>0%</span>
                                    </div>
                                    <div class="progress media-upload-progress__bar-wrap">
                                        <div
                                            class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar"
                                            style="width: 0%;"
                                            aria-valuenow="0"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                            data-upload-progress-bar
                                        ></div>
                                    </div>
                                </div>

                                <div class="row align-items-center">
                                    <div class="col-lg-7">
                                        <div class="media-public-toggle">
                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input" type="checkbox" role="switch" value="1" id="is_public" name="is_public" {{ $isPublic ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_public">Public (Visible in public media sections)</label>
                                            </div>
                                            <small class="text-muted">Turn this on if the asset should appear on the public website.</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="card-action mt-3 mt-lg-0 justify-content-lg-end">
                                            <a href="{{ route('dashboard.media.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary media-submit-button" data-submit-button>
                                                <i class="fas fa-cloud-upload-alt me-2"></i>{{ $submitLabel }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <style>
        .media-upload-shell { border: 0; overflow: hidden; box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08); }
        .media-upload-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 18px; padding: 24px 24px 18px; background: radial-gradient(circle at top right, rgba(14, 165, 233, 0.12), transparent 34%), linear-gradient(180deg, #fcfdff 0%, #f8fbff 100%); border-bottom: 1px solid #e9eef5; }
        .media-upload-eyebrow { font-size: 0.78rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; color: #0284c7; margin-bottom: 6px; }
        .media-upload-subtitle { max-width: 720px; color: #64748b; font-size: 0.95rem; }
        .media-upload-badges { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 10px; }
        .media-upload-badge { display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 999px; background: #fff; border: 1px solid #dbe7f3; color: #0f172a; font-size: 0.85rem; font-weight: 600; }
        .media-quick-guide { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 24px; }
        .guide-step { display: flex; gap: 14px; padding: 16px; border-radius: 16px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .guide-step-number { width: 34px; height: 34px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0ea5e9, #2563eb); color: #fff; font-weight: 700; flex-shrink: 0; }
        .guide-step h6 { margin-bottom: 4px; font-size: 0.95rem; }
        .guide-step p { margin: 0; color: #64748b; font-size: 0.86rem; }
        .media-section-card { padding: 20px; border-radius: 20px; background: #fff; border: 1px solid #e2e8f0; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04); }
        .media-section-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 16px; }
        .media-section-head p { color: #64748b; }
        .media-guidance-banner { display: flex; gap: 14px; align-items: flex-start; padding: 16px 18px; border-radius: 18px; background: linear-gradient(135deg, #eff6ff, #f8fafc); border: 1px solid #dbeafe; }
        .guidance-icon { width: 42px; height: 42px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #2563eb, #0ea5e9); color: #fff; flex-shrink: 0; }
        .media-type-switcher { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .media-type-chip { border: 1px solid #dbe3ef; border-radius: 18px; background: #fff; padding: 16px; text-align: left; transition: all 0.2s ease; display: flex; flex-direction: column; gap: 6px; color: #0f172a; }
        .media-type-chip i { font-size: 1.1rem; color: #2563eb; }
        .media-type-chip span { font-weight: 700; }
        .media-type-chip small { color: #64748b; }
        .media-type-chip:hover, .media-type-chip.is-active { border-color: #60a5fa; background: linear-gradient(180deg, #f8fbff, #eff6ff); box-shadow: 0 10px 24px rgba(37, 99, 235, 0.08); transform: translateY(-1px); }
        .crop-frame { position: relative; width: 100%; max-width: 540px; aspect-ratio: 4 / 3; overflow: hidden; border: 2px dashed #ced4da; border-radius: 12px; background: #f8f9fa; margin-top: 12px; touch-action: none; }
        .crop-frame img { position: absolute; user-select: none; max-width: none; cursor: grab; -webkit-user-drag: none; touch-action: none; }
        .crop-frame img.is-dragging { cursor: grabbing; }
        .crop-overlay { position: absolute; inset: 0; display: flex; align-items: flex-end; justify-content: center; padding: 16px; pointer-events: none; background: linear-gradient(to bottom, rgba(15, 23, 42, 0.08), rgba(15, 23, 42, 0.2)), repeating-linear-gradient(90deg, rgba(255, 255, 255, 0.22) 0, rgba(255, 255, 255, 0.22) 1px, transparent 1px, transparent 33.333%), repeating-linear-gradient(0deg, rgba(255, 255, 255, 0.22) 0, rgba(255, 255, 255, 0.22) 1px, transparent 1px, transparent 33.333%); }
        .crop-overlay span { padding: 6px 10px; border-radius: 999px; background: rgba(15, 23, 42, 0.75); color: #fff; font-size: 0.85rem; line-height: 1; }
        .cropper-card { border: 1px solid #e5e7eb; border-radius: 18px; padding: 18px; background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%); box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8); }
        .media-existing-preview img, .crop-result img, .media-thumb { aspect-ratio: 4 / 3; object-fit: cover; }
        .cropper-state { min-height: 24px; font-weight: 500; }
        .media-public-toggle { padding: 16px 18px; border-radius: 16px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .media-submit-button { min-width: 190px; border-radius: 12px; box-shadow: 0 12px 20px rgba(37, 99, 235, 0.18); }
        .media-upload-progress { padding: 16px 18px; border-radius: 18px; border: 1px solid #dbeafe; background: linear-gradient(180deg, #eff6ff 0%, #f8fbff 100%); }
        .media-upload-progress__head { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 12px; }
        .media-upload-progress__percent { font-size: 1rem; font-weight: 700; color: #1d4ed8; min-width: 52px; text-align: right; }
        .media-upload-progress__bar-wrap { height: 14px; border-radius: 999px; background: rgba(37, 99, 235, 0.12); overflow: hidden; }
        .media-upload-progress__bar-wrap .progress-bar { background: linear-gradient(90deg, #2563eb 0%, #0ea5e9 100%); }
        .you-tube-panel { margin-top: 24px; padding: 18px; border-radius: 18px; background: linear-gradient(180deg, #fff 0%, #fff8f4 100%); border: 1px solid #fde3d3; }
        .you-tube-panel__head { display: flex; justify-content: space-between; gap: 16px; align-items: flex-start; margin-bottom: 16px; }
        .shorts-guidance, .video-meta-box { padding: 14px 16px; border-radius: 14px; background: #fff; border: 1px solid #f6d2bb; }
        .current-youtube-status { padding: 12px 16px; border-radius: 14px; background: #fff; border: 1px solid #f4e1d6; }
        @media (max-width: 991px) { .media-upload-header { flex-direction: column; } .media-upload-badges { justify-content: flex-start; } .media-quick-guide, .media-type-switcher { grid-template-columns: 1fr; } .you-tube-panel__head { flex-direction: column; } }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('[data-media-form]');
            if (!form) return;

            const submitButton = form.querySelector('[data-submit-button]');
            const mediaTypeSelect = form.querySelector('[data-media-type-select]');
            const panels = form.querySelectorAll('[data-media-panel]');
            const typeChips = form.querySelectorAll('[data-type-chip]');
            const videoInput = form.querySelector('[data-video-input]');
            const audioInput = form.querySelector('[data-audio-input]');
            const videoPreview = form.querySelector('[data-video-preview]');
            const audioPreview = form.querySelector('[data-audio-preview]');
            const youtubeToggle = form.querySelector('[data-youtube-toggle]');
            const youtubeFields = form.querySelector('[data-youtube-fields]');
            const youtubeFormat = form.querySelector('[data-youtube-format]');
            const youtubeTitle = form.querySelector('[data-youtube-title]');
            const mainTitle = form.querySelector('[data-main-title]');
            const shortsStatus = form.querySelector('[data-shorts-status]');
            const metaStatus = form.querySelector('[data-video-meta-status]');
            const hiddenDuration = form.querySelector('[data-video-duration]');
            const hiddenWidth = form.querySelector('[data-video-width]');
            const hiddenHeight = form.querySelector('[data-video-height]');
            const youtubeConnected = form.dataset.youtubeConnected === 'true';
            const progressWrapper = form.querySelector('[data-upload-progress]');
            const progressBar = form.querySelector('[data-upload-progress-bar]');
            const progressLabel = form.querySelector('[data-upload-progress-label]');
            const progressPercent = form.querySelector('[data-upload-progress-percent]');
            const targetWidth = 1600;
            const targetHeight = 1200;
            let isSubmitting = false;

            class FixedRatioCropper {
                constructor(type) {
                    this.type = type;
                    this.fileInput = form.querySelector(`[data-source-input="${type}"]`);
                    this.hiddenInput = form.querySelector(`[data-cropped-output="${type}"]`);
                    this.frame = form.querySelector(`[data-crop-frame="${type}"]`);
                    this.image = form.querySelector(`[data-crop-image="${type}"]`);
                    this.zoom = form.querySelector(`[data-crop-zoom="${type}"]`);
                    this.controls = form.querySelector(`[data-crop-controls="${type}"]`);
                    this.confirmButton = form.querySelector(`[data-crop-confirm="${type}"]`);
                    this.resetButton = form.querySelector(`[data-crop-reset="${type}"]`);
                    this.result = form.querySelector(`[data-crop-result="${type}"]`);
                    this.resultWrapper = form.querySelector(`[data-crop-result-wrapper="${type}"]`);
                    this.status = form.querySelector(`[data-crop-status="${type}"]`);
                    this.hasExisting = form.querySelector(`[data-cropper="${type}"]`)?.dataset.hasExisting === 'true';
                    this.sourceUrl = null;
                    this.naturalWidth = 0;
                    this.naturalHeight = 0;
                    this.baseWidth = 0;
                    this.baseHeight = 0;
                    this.displayWidth = 0;
                    this.displayHeight = 0;
                    this.offsetX = 0;
                    this.offsetY = 0;
                    this.dragging = false;
                    this.pointerId = null;
                    this.startPointerX = 0;
                    this.startPointerY = 0;
                    this.startOffsetX = 0;
                    this.startOffsetY = 0;

                    if (!this.fileInput) return;

                    this.bindEvents();

                    if (this.hiddenInput.value) {
                        this.result.src = this.hiddenInput.value;
                        this.resultWrapper.classList.remove('d-none');
                        this.status.textContent = 'A cropped asset is ready for upload.';
                    }
                }

                bindEvents() {
                    this.fileInput.addEventListener('change', (event) => {
                        const [file] = event.target.files;
                        if (!file) return;
                        this.loadFile(file);
                        updateSubmitState();
                    });
                    this.zoom.addEventListener('input', () => this.updateZoom(parseFloat(this.zoom.value)));
                    this.confirmButton.addEventListener('click', () => {
                        this.exportCrop();
                        updateSubmitState();
                    });
                    this.resetButton.addEventListener('click', () => {
                        if (this.fileInput.files.length > 0) this.loadFile(this.fileInput.files[0]);
                    });
                    this.frame.addEventListener('pointerdown', (event) => {
                        if (!this.sourceUrl) return;
                        event.preventDefault();
                        this.dragging = true;
                        this.pointerId = event.pointerId;
                        this.startPointerX = event.clientX;
                        this.startPointerY = event.clientY;
                        this.startOffsetX = this.offsetX;
                        this.startOffsetY = this.offsetY;
                        this.image.classList.add('is-dragging');
                        this.frame.setPointerCapture(event.pointerId);
                    });
                    this.frame.addEventListener('pointermove', (event) => {
                        if (!this.dragging || event.pointerId !== this.pointerId) return;
                        event.preventDefault();
                        this.offsetX = this.startOffsetX + (event.clientX - this.startPointerX);
                        this.offsetY = this.startOffsetY + (event.clientY - this.startPointerY);
                        this.clampOffsets();
                        this.render();
                    });
                    const endDrag = (event) => {
                        if (!this.dragging || event.pointerId !== this.pointerId) return;
                        event.preventDefault();
                        this.dragging = false;
                        this.image.classList.remove('is-dragging');
                        this.frame.releasePointerCapture(event.pointerId);
                    };
                    this.frame.addEventListener('pointerup', endDrag);
                    this.frame.addEventListener('pointercancel', endDrag);
                    this.image.addEventListener('dragstart', (event) => event.preventDefault());
                }

                loadFile(file) {
                    if (this.sourceUrl) URL.revokeObjectURL(this.sourceUrl);
                    this.hiddenInput.value = '';
                    this.result.src = '';
                    this.resultWrapper.classList.add('d-none');
                    this.status.textContent = 'Adjust the image inside the frame, then click the crop button.';
                    this.zoom.value = '1';
                    this.sourceUrl = URL.createObjectURL(file);
                    this.image.onload = () => {
                        this.naturalWidth = this.image.naturalWidth;
                        this.naturalHeight = this.image.naturalHeight;
                        this.setupInitialLayout();
                    };
                    this.image.setAttribute('draggable', 'false');
                    this.image.src = this.sourceUrl;
                }

                setupInitialLayout() {
                    const frameWidth = this.frame.clientWidth;
                    const frameHeight = this.frame.clientHeight;
                    const scale = Math.max(frameWidth / this.naturalWidth, frameHeight / this.naturalHeight);
                    this.baseWidth = this.naturalWidth * scale;
                    this.baseHeight = this.naturalHeight * scale;
                    this.displayWidth = this.baseWidth;
                    this.displayHeight = this.baseHeight;
                    this.offsetX = (frameWidth - this.displayWidth) / 2;
                    this.offsetY = (frameHeight - this.displayHeight) / 2;
                    this.clampOffsets();
                    this.frame.classList.remove('d-none');
                    this.controls.classList.remove('d-none');
                    this.render();
                }

                updateZoom(zoomValue) {
                    if (!this.sourceUrl) return;
                    const frameWidth = this.frame.clientWidth;
                    const frameHeight = this.frame.clientHeight;
                    const centerRatioX = (frameWidth / 2 - this.offsetX) / this.displayWidth;
                    const centerRatioY = (frameHeight / 2 - this.offsetY) / this.displayHeight;
                    this.displayWidth = this.baseWidth * zoomValue;
                    this.displayHeight = this.baseHeight * zoomValue;
                    this.offsetX = frameWidth / 2 - centerRatioX * this.displayWidth;
                    this.offsetY = frameHeight / 2 - centerRatioY * this.displayHeight;
                    this.clampOffsets();
                    this.render();
                }

                clampOffsets() {
                    const frameWidth = this.frame.clientWidth;
                    const frameHeight = this.frame.clientHeight;
                    const minX = frameWidth - this.displayWidth;
                    const minY = frameHeight - this.displayHeight;
                    this.offsetX = Math.min(0, Math.max(minX, this.offsetX));
                    this.offsetY = Math.min(0, Math.max(minY, this.offsetY));
                }

                render() {
                    this.image.style.width = `${this.displayWidth}px`;
                    this.image.style.height = `${this.displayHeight}px`;
                    this.image.style.left = `${this.offsetX}px`;
                    this.image.style.top = `${this.offsetY}px`;
                }

                exportCrop() {
                    if (!this.sourceUrl) return;
                    const frameWidth = this.frame.clientWidth;
                    const frameHeight = this.frame.clientHeight;
                    const sourceX = (-this.offsetX / this.displayWidth) * this.naturalWidth;
                    const sourceY = (-this.offsetY / this.displayHeight) * this.naturalHeight;
                    const sourceWidth = (frameWidth / this.displayWidth) * this.naturalWidth;
                    const sourceHeight = (frameHeight / this.displayHeight) * this.naturalHeight;
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.width = targetWidth;
                    canvas.height = targetHeight;
                    context.drawImage(this.image, sourceX, sourceY, sourceWidth, sourceHeight, 0, 0, targetWidth, targetHeight);
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
                    this.hiddenInput.value = dataUrl;
                    this.result.src = dataUrl;
                    this.resultWrapper.classList.remove('d-none');
                    this.status.textContent = this.type === 'image'
                        ? 'Cropped image ready. This result will be uploaded.'
                        : 'Cropped thumbnail ready. This result will be uploaded.';
                }

                requiresCrop() {
                    if (mediaTypeSelect.value !== this.type) return false;
                    if (this.fileInput.files.length > 0) return true;
                    return !this.hasExisting;
                }

                isReady() {
                    return !this.requiresCrop() || Boolean(this.hiddenInput.value);
                }
            }

            const imageCropper = new FixedRatioCropper('image');
            const videoCropper = new FixedRatioCropper('video');

            function togglePanels() {
                const selectedType = mediaTypeSelect.value;
                panels.forEach((panel) => {
                    const isActive = panel.dataset.mediaPanel === selectedType;
                    panel.classList.toggle('d-none', !isActive);
                    panel.querySelectorAll('[data-panel-input]').forEach((input) => {
                        input.disabled = !isActive;
                    });
                });
                typeChips.forEach((chip) => chip.classList.toggle('is-active', chip.dataset.typeChip === selectedType));
                toggleYouTubeFields();
                updateSubmitState();
            }

            function updateVideoPreview() {
                const file = videoInput?.files?.[0];
                if (!videoPreview || !file) return;
                videoPreview.src = URL.createObjectURL(file);
                videoPreview.classList.remove('d-none');
                detectVideoMetadata(videoPreview.src);
            }

            function updateAudioPreview() {
                const file = audioInput?.files?.[0];
                if (!audioPreview || !file) return;
                audioPreview.src = URL.createObjectURL(file);
                audioPreview.classList.remove('d-none');
            }

            function detectVideoMetadata(sourceUrl) {
                if (!sourceUrl) return;
                const probe = document.createElement('video');
                probe.preload = 'metadata';
                probe.onloadedmetadata = function () {
                    hiddenDuration.value = Math.round(probe.duration || 0);
                    hiddenWidth.value = probe.videoWidth || 0;
                    hiddenHeight.value = probe.videoHeight || 0;
                    metaStatus.textContent = `Detected ${probe.videoWidth}x${probe.videoHeight} and ${Math.round(probe.duration)} seconds.`;
                    updateShortsStatus();
                };
                probe.onerror = function () {
                    metaStatus.textContent = 'Could not detect video metadata automatically.';
                    updateShortsStatus();
                };
                probe.src = sourceUrl;
            }

            function toggleYouTubeFields() {
                if (!youtubeToggle || !youtubeFields) return;
                const active = mediaTypeSelect.value === 'video' && youtubeConnected && youtubeToggle.checked;
                youtubeFields.classList.toggle('d-none', !active);
            }

            function updateShortsStatus() {
                if (!shortsStatus || !youtubeFormat) return;
                if (youtubeFormat.value !== 'short') {
                    shortsStatus.textContent = 'Full video selected. Shorts validation is not active.';
                    return;
                }
                const duration = Number(hiddenDuration.value || 0);
                const width = Number(hiddenWidth.value || 0);
                const height = Number(hiddenHeight.value || 0);
                if (!duration || !width || !height) {
                    shortsStatus.textContent = 'Short requires detected duration and dimensions from the selected video.';
                    return;
                }
                const isVerticalOrSquare = height >= width;
                const isShortEnough = duration <= 180;
                shortsStatus.textContent = isVerticalOrSquare && isShortEnough
                    ? `Ready for Shorts: ${width}x${height}, ${duration}s.`
                    : `Not Shorts-ready yet. Needs vertical or square video and 180 seconds or less. Current file is ${width}x${height}, ${duration}s.`;
            }

            function updateSubmitState() {
                if (isSubmitting) {
                    submitButton.disabled = true;
                    return;
                }
                const selectedType = mediaTypeSelect.value;
                let disabled = false;
                if (selectedType === 'image' && !imageCropper.isReady()) disabled = true;
                if (selectedType === 'video' && !videoCropper.isReady()) disabled = true;
                submitButton.disabled = disabled;
            }

            function setUploadProgress(percent, label) {
                if (!progressWrapper || !progressBar || !progressPercent || !progressLabel) return;
                const bounded = Math.max(0, Math.min(100, Math.round(percent)));
                progressWrapper.classList.remove('d-none');
                progressBar.style.width = `${bounded}%`;
                progressBar.setAttribute('aria-valuenow', `${bounded}`);
                progressPercent.textContent = `${bounded}%`;
                progressLabel.textContent = label;
            }

            function resetUploadState() {
                isSubmitting = false;
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = `<i class="fas fa-cloud-upload-alt me-2"></i>{{ $submitLabel }}`;
                }
                updateSubmitState();
            }

            typeChips.forEach((chip) => {
                chip.addEventListener('click', () => {
                    mediaTypeSelect.value = chip.dataset.typeChip;
                    togglePanels();
                });
            });

            mediaTypeSelect.addEventListener('change', togglePanels);
            videoInput?.addEventListener('change', updateVideoPreview);
            audioInput?.addEventListener('change', updateAudioPreview);
            youtubeToggle?.addEventListener('change', () => {
                toggleYouTubeFields();
                updateShortsStatus();
            });
            youtubeFormat?.addEventListener('change', updateShortsStatus);
            mainTitle?.addEventListener('input', () => {
                if (youtubeTitle && (!youtubeTitle.value || youtubeTitle.dataset.autofill === 'true')) {
                    youtubeTitle.value = mainTitle.value;
                    youtubeTitle.dataset.autofill = 'true';
                }
            });
            youtubeTitle?.addEventListener('input', () => {
                youtubeTitle.dataset.autofill = youtubeTitle.value === mainTitle.value ? 'true' : 'false';
            });

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                if (isSubmitting) return;

                const selectedType = mediaTypeSelect.value;
                if ((selectedType === 'image' && !imageCropper.isReady()) || (selectedType === 'video' && !videoCropper.isReady())) {
                    updateSubmitState();
                    return;
                }

                isSubmitting = true;
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Uploading...';
                setUploadProgress(0, 'Preparing upload...');

                const xhr = new XMLHttpRequest();
                xhr.open(form.method || 'POST', form.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'text/html,application/xhtml+xml');

                xhr.upload.addEventListener('progress', function (uploadEvent) {
                    if (!uploadEvent.lengthComputable) {
                        setUploadProgress(10, 'Uploading media...');
                        return;
                    }

                    const percent = (uploadEvent.loaded / uploadEvent.total) * 100;
                    setUploadProgress(percent, percent >= 100 ? 'Finalizing upload...' : 'Uploading media...');
                });

                xhr.addEventListener('load', function () {
                    if (xhr.status >= 200 && xhr.status < 400) {
                        setUploadProgress(100, 'Upload complete. Redirecting...');
                        if (xhr.responseURL) {
                            window.history.replaceState({}, '', xhr.responseURL);
                        }
                        document.open();
                        document.write(xhr.responseText);
                        document.close();
                        return;
                    }

                    resetUploadState();
                    setUploadProgress(0, 'Upload failed. Please try again.');
                    progressWrapper?.classList.add('border', 'border-danger-subtle');
                    alert('The upload could not be completed. Please review the form and try again.');
                });

                xhr.addEventListener('error', function () {
                    resetUploadState();
                    setUploadProgress(0, 'Network error while uploading.');
                    progressWrapper?.classList.add('border', 'border-danger-subtle');
                    alert('A network error interrupted the upload. Please try again.');
                });

                xhr.addEventListener('abort', function () {
                    resetUploadState();
                    setUploadProgress(0, 'Upload cancelled.');
                });

                xhr.send(new FormData(form));
            });

            if (videoPreview?.getAttribute('src')) detectVideoMetadata(videoPreview.getAttribute('src'));
            togglePanels();
            updateShortsStatus();
        });
    </script>
@endpush
