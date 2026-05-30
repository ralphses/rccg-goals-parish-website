<x-app-layout title="Stream Settings">
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Stream Settings</div>
                        </div>
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
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
                                            <input type="text" class="form-control" id="title" name="title"
                                                value="{{ old('title', $stream->title ?? '') }}" required>
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
                                            <input type="url" class="form-control" id="youtube_url"
                                                name="youtube_url"
                                                value="{{ old('youtube_url', $stream->youtube_url ?? '') }}" required>
                                            <small class="form-text text-muted">Enter the full YouTube video URL
                                                (e.g.,
                                                https://www.youtube.com/watch?v=...)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="scheduled_at">Scheduled At</label>
                                            <input type="datetime-local" class="form-control" id="scheduled_at"
                                                name="scheduled_at"
                                                value="{{ old('scheduled_at', $stream && $stream->scheduled_at ? $stream->scheduled_at->format('Y-m-d\TH:i') : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Is Live</label>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" id="is_live"
                                                    name="is_live" value="1"
                                                    {{ old('is_live', optional($stream)->is_live ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_live">Toggle to set service
                                                    live</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
 
                                <div class="card-action">
                                    <button type="submit" class="btn btn-primary">Update Stream</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Video Preview</h4>
                        </div>
                        <div class="card-body">
                            <div id="video-preview-container" class="text-center">
                                <p id="preview-placeholder">Enter a YouTube URL to see a preview.</p>
                                <iframe id="video-preview" width="100%" height="200" src=""
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen style="display: none;"></iframe>
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
 
                // Initial check
                updatePreview(youtubeUrlInput.value);
 
                // Update on input
                youtubeUrlInput.addEventListener('input', function() {
                    updatePreview(this.value);
                });
            });
        </script>
    @endpush
</x-app-layout>