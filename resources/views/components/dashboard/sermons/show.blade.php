@props(['sermon'])
@include('components.dashboard.partials.show-shell')

<div class="container">
    <div class="page-inner">
        <div class="show-shell">
            <div class="show-hero card mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="show-eyebrow">Sermon View</span>
                            <h2 class="show-title">{{ $sermon->title }}</h2>
                            <p class="show-subtitle">Review the sermon’s cover image, preacher, message, media assets, and attachments from a more polished archive view.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="show-hero-actions">
                                <div class="show-action-row">
                                    <a href="{{ route('dashboard.sermons.index') }}" class="btn btn-outline-secondary btn-lg">Back to Sermons</a>
                                    @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                        <a href="{{ route('dashboard.sermons.edit', $sermon) }}" class="btn btn-primary btn-lg show-primary-btn">Edit Sermon</a>
                                    @endif
                                </div>
                                <div class="show-hero-note"><span class="dot"></span>Preached {{ $sermon->sermon_date->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#eef6ff;color:#1d4ed8;"><i class="fas fa-user-microphone"></i></div><div><div class="show-stat-value">{{ $sermon->speaker->name ?? 'N/A' }}</div><div class="show-stat-label">Speaker</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-clock"></i></div><div><div class="show-stat-value">{{ $sermon->duration ?: 'N/A' }}</div><div class="show-stat-label">Duration</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-calendar-day"></i></div><div><div class="show-stat-value">{{ $sermon->sermon_date->format('d M') }}</div><div class="show-stat-label">Sermon Date</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="show-stat-card"><div class="show-stat-icon" style="background:#f8fafc;color:#475569;"><i class="fas fa-signal"></i></div><div><div class="show-stat-value">{{ ucfirst((string) $sermon->status) }}</div><div class="show-stat-label">Publishing Status</div></div></div></div>
            </div>

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="show-detail-card">
                        <div class="show-card-header">
                            <h3 class="show-card-title">Sermon Overview</h3>
                            <p class="show-card-subtitle">Primary sermon content, description, and message body.</p>
                        </div>
                        <div class="show-card-body">
                            @if ($sermon->cover_image_url)
                                <div class="show-media-frame visual mb-4">
                                    <img src="{{ $sermon->cover_image_url }}" alt="{{ $sermon->title }}">
                                </div>
                            @endif

                            <div class="show-meta-grid mb-4">
                                <div class="show-meta-item"><span class="show-meta-label">Speaker</span><div class="show-meta-value">{{ $sermon->speaker->name ?? 'Unknown speaker' }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Sermon Date</span><div class="show-meta-value">{{ $sermon->sermon_date->format('d M, Y') }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Duration</span><div class="show-meta-value">{{ $sermon->duration ?: 'N/A' }}</div></div>
                                <div class="show-meta-item"><span class="show-meta-label">Status</span><div class="show-meta-value"><span class="show-pill info">{{ ucfirst((string) $sermon->status) }}</span></div></div>
                            </div>

                            <h4 class="mb-3">Description</h4>
                            <div class="show-content-block mb-4">{{ $sermon->description }}</div>

                            <h4 class="mb-3">Message</h4>
                            <div class="show-content-block">{!! $sermon->message !!}</div>
                        </div>
                    </div>

                    @if ($sermon->audio_url || $sermon->video_url)
                        <div class="show-detail-card mt-4">
                            <div class="show-card-header">
                                <h3 class="show-card-title">Sermon Media</h3>
                                <p class="show-card-subtitle">Available audio and video playback resources.</p>
                            </div>
                            <div class="show-card-body">
                                @if ($sermon->audio_url)
                                    <h4 class="mb-3">Audio</h4>
                                    <div class="show-content-block mb-4">
                                        <audio controls class="w-100">
                                            <source src="{{ $sermon->audio_url }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>
                                @endif

                                @if ($sermon->video_url)
                                    <h4 class="mb-3">Video</h4>
                                    <div class="show-media-frame video">
                                        <iframe src="{{ $sermon->video_url }}" title="Sermon Video" allowfullscreen></iframe>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-xl-4">
                    <div class="show-side-card">
                        <div class="show-card-header">
                            <h3 class="show-card-title">Attachments</h3>
                            <p class="show-card-subtitle">Downloadable files linked to this sermon.</p>
                        </div>
                        <div class="show-card-body">
                            @if ($sermon->attachments->count() > 0)
                                <div class="show-attachment-list">
                                    @foreach ($sermon->attachments as $attachment)
                                        <div class="show-attachment-item">
                                            <div>
                                                <div class="fw-bold text-dark">{{ $attachment->file_name }}</div>
                                                <small class="text-muted">{{ $attachment->file_type }}</small>
                                            </div>
                                            <a href="{{ route('dashboard.sermon-attachments.download', $attachment) }}" class="btn btn-sm btn-outline-primary">Download</a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="show-empty-state">
                                    <i class="fas fa-paperclip"></i>
                                    <p class="mb-0">No attachments for this sermon.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
