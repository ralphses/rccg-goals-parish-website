@props(['media'])
@include('components.dashboard.partials.show-shell')

@php
    $typePill = match ($media->media_type) {
        \App\enums\MediaType::IMAGE => 'info',
        \App\enums\MediaType::VIDEO => 'dark',
        \App\enums\MediaType::AUDIO => 'warning',
    };

    $uploadPill = match ($media->upload_status ?? null) {
        \App\enums\MediaUploadStatus::READY => 'success',
        \App\enums\MediaUploadStatus::FAILED => 'danger',
        \App\enums\MediaUploadStatus::PROCESSING => 'info',
        \App\enums\MediaUploadStatus::QUEUED => 'warning',
        default => 'neutral',
    };

    $youtubePill = match ($media->youtube_status ?? null) {
        \App\enums\YouTubePublishStatus::UPLOADED_PRIVATE, \App\enums\YouTubePublishStatus::PUBLISHED => 'success',
        \App\enums\YouTubePublishStatus::FAILED => 'danger',
        \App\enums\YouTubePublishStatus::UPLOADING => 'info',
        \App\enums\YouTubePublishStatus::QUEUED => 'warning',
        default => 'neutral',
    };

    $uploadedBy = match ($media->mediable_type) {
        'App\\Models\\User' => $media->mediable->name ?? 'N/A',
        'App\\Models\\Testimony' => $media->mediable->testifier_name ?? 'N/A',
        default => 'N/A',
    };
@endphp

<div class="container">
    <div class="page-inner">
        <div class="show-shell">
            <div class="show-hero card mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="show-eyebrow">Media View</span>
                            <h2 class="show-title">{{ $media->title }}</h2>
                            <p class="show-subtitle">Inspect the uploaded asset, app processing state, public playback readiness, and YouTube publishing details from one operational view.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="show-hero-actions">
                                <div class="show-action-row">
                                    <a href="{{ route('dashboard.media.index') }}" class="btn btn-outline-secondary btn-lg">Back to Media</a>
                                    <a href="{{ route('dashboard.media.edit', $media->id) }}" class="btn btn-primary btn-lg show-primary-btn">Edit Media</a>
                                </div>
                                <div class="show-hero-note"><span class="dot"></span>{{ $media->created_at->diffForHumans() }} upload record</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#eef6ff;color:#1d4ed8;"><i class="fas fa-file"></i></div><div><div class="show-stat-value">{{ ucwords($media->media_type->value) }}</div><div class="show-stat-label">Media Type</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#f8fafc;color:#475569;"><i class="fas fa-folder-open"></i></div><div><div class="show-stat-value">{{ ucwords(str_replace('_', ' ', $media->category->value)) }}</div><div class="show-stat-label">Category</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-database"></i></div><div><div class="show-stat-value">{{ number_format($media->size / 1024, 2) }} KB</div><div class="show-stat-label">Stored Size</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:{{ $media->is_public ? '#dcfce7' : '#fee2e2' }};color:{{ $media->is_public ? '#15803d' : '#dc2626' }};"><i class="fas fa-globe"></i></div><div><div class="show-stat-value">{{ $media->is_public ? 'Public' : 'Private' }}</div><div class="show-stat-label">Visibility</div></div></div></div>
            </div>

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="show-detail-card">
                        <div class="show-card-header">
                            <h3 class="show-card-title">Media Preview</h3>
                            <p class="show-card-subtitle">Primary asset preview with current readiness status.</p>
                        </div>
                        <div class="show-card-body">
                            @if ($media->media_type === \App\enums\MediaType::IMAGE)
                                <div class="show-media-frame visual mb-4">
                                    <img src="{{ $media->visual_url }}" alt="{{ $media->title }}">
                                </div>
                            @elseif ($media->media_type === \App\enums\MediaType::VIDEO)
                                @if ($media->thumbnail_path)
                                    <div class="show-media-frame visual mb-4">
                                        <img src="{{ $media->visual_url }}" alt="{{ $media->title }}">
                                    </div>
                                @endif

                                @if ($media->upload_status === \App\enums\MediaUploadStatus::READY && $media->file_url)
                                    <div class="show-media-frame visual mb-4">
                                        <video controls>
                                            <source src="{{ $media->file_url }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                @else
                                    <div class="alert alert-info rounded-4 mb-4">
                                        The public video file is not ready yet. Current app upload status:
                                        <strong>{{ ucwords(str_replace('_', ' ', $media->upload_status->value)) }}</strong>.
                                    </div>
                                @endif
                            @elseif ($media->media_type === \App\enums\MediaType::AUDIO)
                                <div class="show-content-block mb-4">
                                    <audio controls class="w-100">
                                        <source src="{{ $media->file_url }}">
                                        Your browser does not support the audio element.
                                    </audio>
                                </div>
                            @endif

                            <div class="show-meta-grid">
                                <div class="show-meta-item"><span class="show-meta-label">Type</span><div class="show-meta-value"><span class="show-pill {{ $typePill }}">{{ ucwords(str_replace('_', ' ', $media->media_type->value)) }}</span></div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Category</span><div class="show-meta-value">{{ ucwords(str_replace('_', ' ', $media->category->value)) }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Uploaded By</span><div class="show-meta-value">{{ $uploadedBy }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Uploaded At</span><div class="show-meta-value">{{ $media->created_at->format('d M, Y') }}</div></div>
                            </div>
                        </div>
                    </div>

                    @if ($media->media_type === \App\enums\MediaType::VIDEO)
                        <div class="show-detail-card mt-4">
                            <div class="show-card-header">
                                <h3 class="show-card-title">Video Processing & YouTube</h3>
                                <p class="show-card-subtitle">Cloudinary upload progress, source-copy state, and YouTube publishing details.</p>
                            </div>
                            <div class="show-card-body">
                                <div class="show-meta-grid mb-4">
                                    <div class="show-meta-item"><span class="show-meta-label">App Upload Status</span><div class="show-meta-value"><span class="show-pill {{ $uploadPill }}">{{ ucwords(str_replace('_', ' ', $media->upload_status->value)) }}</span></div></div>
                                    <div class="show-meta-item"><span class="show-meta-label">Upload Activity</span><div class="show-meta-value">{{ $media->updated_at->diffForHumans() }}</div></div>
                                    <div class="show-meta-item"><span class="show-meta-label">YouTube Publish</span><div class="show-meta-value">{{ $media->publish_to_youtube ? 'Enabled' : 'Disabled' }}</div></div>
                                    <div class="show-meta-item"><span class="show-meta-label">Stored Source Copy</span><div class="show-meta-value">{{ $media->youtube_source_path ? 'Available for retry' : 'Missing' }}</div></div>
                                </div>

                                @if ($media->upload_queued_at)
                                    <div class="alert alert-info rounded-4">App upload queued {{ $media->upload_queued_at->diffForHumans() }}.</div>
                                @endif
                                @if ($media->upload_completed_at)
                                    <div class="alert alert-success rounded-4">App upload completed {{ $media->upload_completed_at->diffForHumans() }}.</div>
                                @endif
                                @if ($media->upload_status === \App\enums\MediaUploadStatus::QUEUED)
                                    <div class="alert alert-info rounded-4">This video file is waiting for the background upload worker to send it to Cloudinary.</div>
                                @elseif ($media->upload_status === \App\enums\MediaUploadStatus::PROCESSING)
                                    <div class="alert alert-info rounded-4">The app is uploading this video file in the background right now.</div>
                                @elseif ($media->upload_status === \App\enums\MediaUploadStatus::FAILED)
                                    <div class="alert alert-warning rounded-4">The app video upload failed before public playback or YouTube publishing could continue.</div>
                                @endif
                                @if ($media->upload_last_error)
                                    <div class="alert alert-warning rounded-4">{{ $media->upload_last_error }}</div>
                                @endif

                                @if ($media->canRetryUploadProcessing())
                                    <form method="POST" action="{{ route('dashboard.media.upload.retry', $media->id) }}" class="show-action-row mb-4">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary">Retry App Upload From Stored Source Copy</button>
                                    </form>
                                @endif

                                @if ($media->publish_to_youtube)
                                    <div class="show-meta-grid mb-4">
                                        <div class="show-meta-item"><span class="show-meta-label">YouTube Status</span><div class="show-meta-value"><span class="show-pill {{ $youtubePill }}">{{ ucwords(str_replace('_', ' ', $media->youtube_status->value)) }}</span></div></div>
                                        <div class="show-meta-item"><span class="show-meta-label">YouTube Format</span><div class="show-meta-value">{{ $media->youtube_format?->value ? ucwords(str_replace('_', ' ', $media->youtube_format->value)) : 'N/A' }}</div></div>
                                        <div class="show-meta-item"><span class="show-meta-label">Last Queue Activity</span><div class="show-meta-value">{{ $media->updated_at->diffForHumans() }}</div></div>
                                        <div class="show-meta-item"><span class="show-meta-label">Last Queued</span><div class="show-meta-value">{{ $media->youtube_publish_requested_at?->diffForHumans() ?? 'N/A' }}</div></div>
                                    </div>

                                    @if ($media->youtube_status === \App\enums\YouTubePublishStatus::QUEUED)
                                        <div class="alert alert-info rounded-4">This video is waiting for the queue worker to process the YouTube upload job.</div>
                                    @elseif ($media->youtube_status === \App\enums\YouTubePublishStatus::UPLOADING)
                                        <div class="alert alert-info rounded-4">The YouTube upload job is running now.</div>
                                    @elseif ($media->youtube_status === \App\enums\YouTubePublishStatus::FAILED)
                                        <div class="alert alert-warning rounded-4">The YouTube upload failed. Fix the issue below, then retry from the stored private source copy.</div>
                                    @endif

                                    @if ($media->youtube_video_url)
                                        <div class="alert alert-success rounded-4">
                                            YouTube link ready:
                                            <a href="{{ $media->youtube_video_url }}" target="_blank" rel="noopener">Open Video</a>
                                            <button type="button" class="btn btn-sm btn-outline-success ms-2" data-copy-text="{{ $media->youtube_video_url }}">Copy Link</button>
                                        </div>
                                    @endif

                                    @if ($media->youtube_last_error)
                                        <div class="alert alert-warning rounded-4">{{ $media->youtube_last_error }}</div>
                                    @endif

                                    @if ($media->upload_status !== \App\enums\MediaUploadStatus::READY && $media->youtube_status === \App\enums\YouTubePublishStatus::NOT_REQUESTED)
                                        <div class="alert alert-info rounded-4">YouTube publishing will queue automatically after the app upload finishes successfully.</div>
                                    @endif

                                    @if ($media->canRetryYouTubePublish())
                                        <form method="POST" action="{{ route('dashboard.media.youtube.retry', $media->id) }}" class="show-action-row">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger">Retry YouTube Upload From Stored Source Copy</button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-xl-4">
                    <div class="show-side-card">
                        <div class="show-card-header">
                            <h3 class="show-card-title">Operational Summary</h3>
                            <p class="show-card-subtitle">Quick facts for editors and media operators.</p>
                        </div>
                        <div class="show-card-body">
                            <div class="show-side-stack">
                                <div class="show-side-item"><div class="show-side-icon"><i class="fas fa-user"></i></div><div><h6>Uploaded By</h6><p>{{ $uploadedBy }}</p></div></div>
                                <div class="show-side-item"><div class="show-side-icon"><i class="fas fa-clock"></i></div><div><h6>Created</h6><p>{{ $media->created_at->format('d M, Y') }}</p></div></div>
                                <div class="show-side-item"><div class="show-side-icon"><i class="fas fa-eye"></i></div><div><h6>Public Access</h6><p>{{ $media->is_public ? 'Visible publicly' : 'Restricted from public pages' }}</p></div></div>
                                @if ($media->media_type === \App\enums\MediaType::VIDEO && $media->thumbnail_path)
                                    <div class="show-side-item"><div class="show-side-icon"><i class="fas fa-crop-simple"></i></div><div><h6>Thumbnail</h6><p>Stored and cropped for consistent listing display.</p></div></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-copy-text]').forEach(button => {
                button.addEventListener('click', async function () {
                    const text = button.dataset.copyText;
                    if (!text) return;

                    const original = button.textContent.trim();

                    try {
                        await navigator.clipboard.writeText(text);
                        button.textContent = 'Copied';
                    } catch (error) {
                        button.textContent = 'Failed';
                    }

                    setTimeout(() => {
                        button.textContent = original;
                    }, 1800);
                });
            });
        });
    </script>
@endpush
