@include('components.dashboard.partials.form-shell')

<x-app-layout title="Stream Settings">
    <div class="container">
        <div class="page-inner">
            <div class="dashboard-form-shell">
                <div class="dashboard-form-hero card mb-4">
                    <div class="card-body p-4 p-lg-5">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-8">
                                <span class="dashboard-form-eyebrow">Live Stream Control</span>
                                <h2 class="dashboard-form-title">Update the live broadcast setup with clearer publishing controls.</h2>
                                <p class="dashboard-form-subtitle">Manage the stream title, description, YouTube link, schedule, and live status from one cleaner view with an instant preview alongside.</p>
                            </div>
                            <div class="col-lg-4">
                                <div class="dashboard-form-hero-actions">
                                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg dashboard-form-secondary-btn">Back to Dashboard</a>
                                    <div class="dashboard-form-note"><span class="dot"></span>Preview updates as soon as the YouTube link changes</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card dashboard-form-card">
                            <div class="card-header">
                                <div class="card-title">Stream Settings</div>
                            </div>
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show m-3 mb-0" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger m-3 mb-0">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="card-body">
                                <form method="POST" action="{{ route('dashboard.stream.update') }}">
                                    @csrf
                                    @method('patch')

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="title">Title</label>
                                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $stream->title ?? '') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $stream->description ?? '') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="youtube_url">YouTube URL</label>
                                                <input type="url" class="form-control" id="youtube_url" name="youtube_url" value="{{ old('youtube_url', $stream->youtube_url ?? '') }}" required>
                                                <small class="dashboard-form-helper">Enter the full YouTube watch URL, for example `https://www.youtube.com/watch?v=...`.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="scheduled_at">Scheduled At</label>
                                                <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at', $stream && $stream->scheduled_at ? $stream->scheduled_at->format('Y-m-d\TH:i') : '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Is Live</label>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" id="is_live" name="is_live" value="1" {{ old('is_live', optional($stream)->is_live ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_live">Toggle to set the current service live</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dashboard-form-actions">
                                        <button type="submit" class="btn btn-primary dashboard-form-primary-btn">Update Stream</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card dashboard-form-card">
                            <div class="card-header">
                                <div class="card-title">Video Preview</div>
                            </div>
                            <div class="card-body">
                                <div class="dashboard-form-preview-panel text-center">
                                    <p id="preview-placeholder" class="mb-3">Enter a YouTube URL to see a preview.</p>
                                    <iframe id="video-preview" width="100%" height="220" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="display: none; border-radius: 16px;"></iframe>
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
            document.addEventListener('DOMContentLoaded', function() {
                const youtubeUrlInput = document.getElementById('youtube_url');
                const videoPreview = document.getElementById('video-preview');
                const previewPlaceholder = document.getElementById('preview-placeholder');

                function getYouTubeVideoId(url) {
                    if (!url) return null;
                    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                    const match = url.match(regExp);
                    return (match && match[2].length === 11) ? match[2] : null;
                }

                function updatePreview(url) {
                    const videoId = getYouTubeVideoId(url);
                    if (videoId) {
                        videoPreview.src = `https://www.youtube.com/embed/${videoId}`;
                        videoPreview.style.display = 'block';
                        previewPlaceholder.style.display = 'none';
                    } else {
                        videoPreview.style.display = 'none';
                        previewPlaceholder.style.display = 'block';
                    }
                }

                updatePreview(youtubeUrlInput.value);
                youtubeUrlInput.addEventListener('input', function() {
                    updatePreview(this.value);
                });
            });
        </script>
    @endpush
</x-app-layout>
