@props(['media', 'categories', 'youtubeConnected' => false, 'queuedVideoCount' => 0, 'backgroundUploadCount' => 0])

@php
    $items = $media->getCollection();
    $imageCount = $items->where('media_type', \App\enums\MediaType::IMAGE)->count();
    $videoCount = $items->where('media_type', \App\enums\MediaType::VIDEO)->count();
    $audioCount = $items->where('media_type', \App\enums\MediaType::AUDIO)->count();
    $publicCount = $items->where('is_public', true)->count();
@endphp

<div class="container">
    <div class="page-inner">
        <div class="media-index-shell">
            <div class="media-index-hero card border-0">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="media-index-eyebrow">Media Control Center</span>
                            <h2 class="media-index-title mb-2">Manage church media with clearer status, faster actions, and better visibility.</h2>
                            <p class="media-index-subtitle mb-0">
                                Track uploads, spot YouTube issues quickly, and keep images, videos, and audio organized in one calmer workspace.
                            </p>
                        </div>
                        <div class="col-lg-4">
                            <div class="media-index-hero-actions">
                                <a href="{{ route('dashboard.media.create') }}" class="btn btn-primary btn-lg media-index-primary-btn">
                                    <i class="fas fa-cloud-upload-alt me-2"></i>Upload Media
                                </a>
                                <div class="media-index-hero-note">
                                    <span class="dot"></span>
                                    {{ $media->total() }} total media {{ $media->total() === 1 ? 'item' : 'items' }} in the library
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-xl-3 col-md-6">
                    <div class="media-stat-card">
                        <div class="media-stat-icon icon-images"><i class="fas fa-image"></i></div>
                        <div>
                            <div class="media-stat-value">{{ $imageCount }}</div>
                            <div class="media-stat-label">Images On This Page</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="media-stat-card">
                        <div class="media-stat-icon icon-videos"><i class="fas fa-video"></i></div>
                        <div>
                            <div class="media-stat-value">{{ $videoCount }}</div>
                            <div class="media-stat-label">Videos On This Page</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="media-stat-card">
                        <div class="media-stat-icon icon-audio"><i class="fas fa-music"></i></div>
                        <div>
                            <div class="media-stat-value">{{ $audioCount }}</div>
                            <div class="media-stat-label">Audio On This Page</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="media-stat-card">
                        <div class="media-stat-icon icon-public"><i class="fas fa-globe-africa"></i></div>
                        <div>
                            <div class="media-stat-value">{{ $publicCount }}</div>
                            <div class="media-stat-label">Public Items On This Page</div>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($backgroundUploadCount > 0)
                <div class="media-status-banner info mt-4">
                    <div class="media-status-banner__icon"><i class="fas fa-spinner"></i></div>
                    <div>
                        <strong>{{ $backgroundUploadCount }} video {{ $backgroundUploadCount === 1 ? 'upload is' : 'uploads are' }} processing in the background.</strong>
                        <div class="small text-muted">The media record is already saved, and YouTube publishing will begin only after each app upload is ready.</div>
                    </div>
                </div>
            @endif

            @if ($youtubeConnected && $queuedVideoCount > 0)
                <div class="media-status-banner youtube mt-3">
                    <div class="media-status-banner__icon"><i class="fab fa-youtube"></i></div>
                    <div>
                        <strong>{{ $queuedVideoCount }} YouTube video {{ $queuedVideoCount === 1 ? 'is' : 'are' }} queued or uploading.</strong>
                        <div class="small text-muted">If a video stays queued too long, confirm the queue worker is running. Failed items will show their errors inline.</div>
                    </div>
                </div>
            @endif

            <div class="card media-library-card border-0 mt-4">
                <div class="card-body p-4">
                    <div class="media-toolbar mb-4">
                        <div>
                            <h3 class="media-toolbar-title mb-1">Media Library</h3>
                            <p class="media-toolbar-subtitle mb-0">Browse, filter, and jump into any media record without losing track of upload state.</p>
                        </div>
                        <div class="media-toolbar-badge">
                            Showing {{ $media->count() }} of {{ $media->total() }}
                        </div>
                    </div>

                    <form method="GET" action="{{ route('dashboard.media.index') }}" class="media-filter-form mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-5">
                                <label class="form-label media-filter-label" for="media-search">Search</label>
                                <div class="media-search-wrap">
                                    <i class="fas fa-search"></i>
                                    <input
                                        id="media-search"
                                        type="text"
                                        name="search"
                                        value="{{ request('search') }}"
                                        class="form-control"
                                        placeholder="Search by title..."
                                    >
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label media-filter-label" for="media-category">Category</label>
                                <select id="media-category" name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->value }}" {{ request('category') == $category->value ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $category->value)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label media-filter-label" for="media-sort">Sort</label>
                                <select id="media-sort" name="sort" class="form-select">
                                    <option value="">Latest First</option>
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title A-Z</option>
                                    <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title Z-A</option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <div class="media-filter-actions">
                                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                                    @if (request()->filled('search') || request()->filled('category') || request()->filled('sort'))
                                        <a href="{{ route('dashboard.media.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>

                    @if ($media->count() > 0)
                        <form method="POST" action="{{ route('dashboard.media.bulk-destroy') }}" data-bulk-form>
                            @csrf
                            @method('DELETE')
                            <div class="listing-bulk-bar">
                                <div class="listing-bulk-summary"><strong><span data-selected-count>0</span></strong> media item(s) selected on this page</div>
                                <div class="listing-bulk-actions">
                                    <button type="submit" class="btn btn-outline-danger" data-bulk-submit disabled>Delete Selected</button>
                                </div>
                            </div>
                        <div class="table-responsive media-table-wrap">
                            <table class="table media-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="listing-check-cell"><input type="checkbox" class="form-check-input listing-check-input" data-select-all></th>
                                        <th>Media</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Visibility</th>
                                        <th>Uploaded</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($media as $item)
                                        <tr class="media-row" data-href="{{ route('dashboard.media.show', $item->id) }}">
                                            <td class="listing-check-cell" onclick="event.stopPropagation();">
                                                <input type="checkbox" name="selected_ids[]" value="{{ $item->id }}" class="form-check-input listing-check-input" data-select-item>
                                            </td>
                                            <td>
                                                <div class="media-main-cell">
                                                    <div class="media-thumb-wrap">
                                                        @if ($item->media_type === \App\enums\MediaType::AUDIO)
                                                            <div class="media-audio-thumb">
                                                                <i class="fas fa-music"></i>
                                                            </div>
                                                        @else
                                                            <img src="{{ $item->visual_url }}" class="media-thumb" alt="{{ $item->title }}">
                                                        @endif
                                                    </div>
                                                    <div class="media-main-copy">
                                                        <div class="media-main-title">{{ $item->title }}</div>
                                                        <div class="media-main-meta">
                                                            <span>#{{ $item->id }}</span>
                                                            <span>{{ number_format($item->size / 1024, 2) }} KB</span>
                                                            @if ($item->mediable_type === 'App\\Models\\User')
                                                                <span>{{ $item->mediable->name ?? 'Unknown uploader' }}</span>
                                                            @elseif ($item->mediable_type === 'App\\Models\\Testimony')
                                                                <span>{{ $item->mediable->testifier_name ?? 'Testimony media' }}</span>
                                                            @else
                                                                <span>Attached media</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge media-pill type">
                                                    {{ ucwords(str_replace('_', ' ', $item->media_type->value)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge media-pill category">
                                                    {{ ucwords(str_replace('_', ' ', $item->category->value)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="media-visibility {{ $item->is_public ? 'public' : 'private' }}">
                                                    <i class="fas {{ $item->is_public ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                                    {{ $item->is_public ? 'Public' : 'Private' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="media-date">{{ $item->created_at->format('d M, Y') }}</div>
                                                <div class="media-date-sub">{{ $item->created_at->diffForHumans() }}</div>
                                            </td>
                                            <td>
                                                @if ($item->media_type === \App\enums\MediaType::VIDEO)
                                                    <div class="media-status-stack">
                                                        <span class="media-status-chip {{ $item->upload_status === \App\enums\MediaUploadStatus::FAILED ? 'danger' : ($item->upload_status === \App\enums\MediaUploadStatus::READY ? 'success' : 'info') }}">
                                                            App Upload: {{ ucwords(str_replace('_', ' ', $item->upload_status->value)) }}
                                                        </span>

                                                        @if ($item->publish_to_youtube)
                                                            <span class="media-status-chip dark">
                                                                YouTube: {{ ucwords(str_replace('_', ' ', $item->youtube_status->value)) }}
                                                            </span>
                                                        @else
                                                            <span class="media-status-note">YouTube not requested</span>
                                                        @endif

                                                        <div class="media-status-updated">Updated {{ $item->updated_at->diffForHumans() }}</div>

                                                        @if ($item->upload_status === \App\enums\MediaUploadStatus::FAILED && $item->upload_last_error)
                                                            <div class="media-status-error">{{ \Illuminate\Support\Str::limit($item->upload_last_error, 110) }}</div>
                                                        @elseif ($item->youtube_status === \App\enums\YouTubePublishStatus::FAILED && $item->youtube_last_error)
                                                            <div class="media-status-error">{{ \Illuminate\Support\Str::limit($item->youtube_last_error, 110) }}</div>
                                                        @endif

                                                        @if ($item->youtube_video_url)
                                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                                <a href="{{ $item->youtube_video_url }}" target="_blank" rel="noopener" class="media-inline-link">Open on YouTube</a>
                                                                <button type="button" class="btn btn-light btn-sm media-copy-btn" data-copy-text="{{ $item->youtube_video_url }}">Copy Link</button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="media-status-note">Ready</span>
                                                @endif
                                            </td>
                                            <td class="text-end" onclick="event.stopPropagation();">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm media-action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="{{ route('dashboard.media.show', $item->id) }}">View</a></li>
                                                        <li><a class="dropdown-item" href="{{ route('dashboard.media.edit', $item->id) }}">Edit</a></li>
                                                        @if ($item->canRetryUploadProcessing())
                                                            <li>
                                                                <form method="POST" action="{{ route('dashboard.media.upload.retry', $item->id) }}">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item text-primary">Retry App Upload</button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        @if ($item->canRetryYouTubePublish())
                                                            <li>
                                                                <form method="POST" action="{{ route('dashboard.media.youtube.retry', $item->id) }}">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item text-danger">Retry YouTube Upload</button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <form method="POST" action="{{ route('dashboard.media.destroy', $item->id) }}" onsubmit="return confirm('Are you sure you want to delete this media?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        </form>
                    @else
                        <div class="media-empty-state">
                            <div class="media-empty-icon"><i class="fas fa-photo-video"></i></div>
                            <h4 class="mb-2">No media matched this view</h4>
                            <p class="text-muted mb-3">Try adjusting your search or filters, or upload a fresh item to get started.</p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <a href="{{ route('dashboard.media.create') }}" class="btn btn-primary">Upload Media</a>
                                <a href="{{ route('dashboard.media.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                            </div>
                        </div>
                    @endif

                    @if ($media->hasPages())
                        <div class="row mt-4 align-items-center">
                            <div class="col-lg-6 mb-3 mb-lg-0">
                                <div class="media-pagination-summary">
                                    Showing {{ $media->firstItem() }} to {{ $media->lastItem() }} of {{ $media->total() }} results
                                </div>
                            </div>
                            <div class="col-lg-6 d-flex justify-content-lg-end justify-content-center">
                                <nav aria-label="Pagination">
                                    <ul class="pagination pagination-modern mb-0">
                                        @if ($media->onFirstPage())
                                            <li class="page-item disabled"><span class="page-link">Prev</span></li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $media->previousPageUrl() . '&search=' . request('search') . '&category=' . request('category') }}">
                                                    Prev
                                                </a>
                                            </li>
                                        @endif

                                        @for ($i = 1; $i <= $media->lastPage(); $i++)
                                            @if ($i == $media->currentPage())
                                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                                            @elseif($i == 1 || $i == $media->lastPage() || ($i >= $media->currentPage() - 2 && $i <= $media->currentPage() + 2))
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $media->url($i) . '&search=' . request('search') . '&category=' . request('category') }}">
                                                        {{ $i }}
                                                    </a>
                                                </li>
                                            @elseif($i == $media->currentPage() - 3 || $i == $media->currentPage() + 3)
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            @endif
                                        @endfor

                                        @if ($media->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $media->nextPageUrl() . '&search=' . request('search') . '&category=' . request('category') }}">
                                                    Next
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <style>
        .media-index-shell {
            --media-primary: #0f766e;
            --media-primary-soft: #ecfeff;
            --media-blue-soft: #eef6ff;
            --media-ink: #0f172a;
            --media-muted: #64748b;
            --media-line: #e2e8f0;
        }

        .media-index-hero {
            overflow: hidden;
            border-radius: 28px;
            background:
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.18), transparent 30%),
                radial-gradient(circle at left bottom, rgba(16, 185, 129, 0.12), transparent 28%),
                linear-gradient(135deg, #ffffff 0%, #f6fbff 52%, #f8fffd 100%);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        }

        .media-index-eyebrow {
            display: inline-block;
            margin-bottom: 12px;
            color: var(--media-primary);
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 800;
        }

        .media-index-title {
            color: var(--media-ink);
            font-size: clamp(1.8rem, 2.6vw, 2.6rem);
            line-height: 1.15;
        }

        .media-index-subtitle {
            max-width: 760px;
            color: var(--media-muted);
            font-size: 1rem;
        }

        .media-index-hero-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 14px;
        }

        .media-index-primary-btn {
            border-radius: 16px;
            padding-inline: 24px;
            box-shadow: 0 16px 30px rgba(15, 118, 110, 0.2);
        }

        .media-index-hero-note {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--media-muted);
            font-size: 0.92rem;
        }

        .media-index-hero-note .dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #10b981;
            box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.12);
        }

        .media-stat-card {
            height: 100%;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px;
            border-radius: 20px;
            border: 1px solid var(--media-line);
            background: #fff;
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.05);
        }

        .media-stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .media-stat-icon.icon-images { background: #eff6ff; color: #2563eb; }
        .media-stat-icon.icon-videos { background: #fff7ed; color: #ea580c; }
        .media-stat-icon.icon-audio { background: #f5f3ff; color: #7c3aed; }
        .media-stat-icon.icon-public { background: #ecfeff; color: #0f766e; }

        .media-stat-value {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--media-ink);
            line-height: 1;
        }

        .media-stat-label {
            margin-top: 4px;
            color: var(--media-muted);
            font-size: 0.88rem;
        }

        .media-status-banner {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 16px 18px;
            border-radius: 18px;
            border: 1px solid var(--media-line);
            background: #fff;
        }

        .media-status-banner.info {
            border-color: #bfdbfe;
            background: #f8fbff;
        }

        .media-status-banner.youtube {
            border-color: #fed7d7;
            background: #fff7f7;
        }

        .media-status-banner__icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .media-status-banner.info .media-status-banner__icon {
            background: #dbeafe;
            color: #2563eb;
        }

        .media-status-banner.youtube .media-status-banner__icon {
            background: #fee2e2;
            color: #dc2626;
        }

        .media-library-card {
            border-radius: 26px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        }

        .media-toolbar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
        }

        .media-toolbar-title {
            color: var(--media-ink);
            font-weight: 800;
        }

        .media-toolbar-subtitle,
        .media-pagination-summary {
            color: var(--media-muted);
        }

        .media-toolbar-badge {
            white-space: nowrap;
            padding: 10px 14px;
            border-radius: 999px;
            background: var(--media-primary-soft);
            color: var(--media-primary);
            font-weight: 700;
            font-size: 0.9rem;
        }

        .media-filter-form {
            padding: 20px;
            border-radius: 22px;
            background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
            border: 1px solid var(--media-line);
        }

        .media-filter-label {
            font-size: 0.84rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: var(--media-muted);
        }

        .media-search-wrap {
            position: relative;
        }

        .media-search-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .media-search-wrap .form-control {
            padding-left: 40px;
        }

        .media-filter-form .form-control,
        .media-filter-form .form-select {
            min-height: 48px;
            border-radius: 14px;
            border-color: var(--media-line);
            box-shadow: none;
        }

        .media-filter-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .media-table-wrap {
            border: 1px solid var(--media-line);
            border-radius: 22px;
            overflow: hidden;
        }

        .media-table {
            --bs-table-bg: transparent;
            --bs-table-hover-bg: rgba(15, 118, 110, 0.04);
            margin: 0;
        }

        .media-table thead th {
            background: #f8fafc;
            color: var(--media-muted);
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 800;
            border-bottom: 1px solid var(--media-line);
            padding: 16px 18px;
        }

        .media-table tbody td {
            padding: 18px;
            border-color: #edf2f7;
            vertical-align: middle;
        }

        .media-row {
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .media-main-cell {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 280px;
        }

        .media-thumb-wrap {
            flex-shrink: 0;
        }

        .media-thumb,
        .media-audio-thumb {
            width: 104px;
            height: 78px;
            border-radius: 16px;
            border: 1px solid var(--media-line);
        }

        .media-thumb {
            object-fit: cover;
            display: block;
            background: #f8fafc;
        }

        .media-audio-thumb {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8fafc, #eef2ff);
            color: #475569;
            font-size: 1.2rem;
        }

        .media-main-title {
            color: var(--media-ink);
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 6px;
        }

        .media-main-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 12px;
            color: var(--media-muted);
            font-size: 0.86rem;
        }

        .media-pill {
            border-radius: 999px;
            padding: 8px 12px;
            font-weight: 700;
            font-size: 0.78rem;
        }

        .media-pill.type {
            background: #f1f5f9;
            color: #334155;
        }

        .media-pill.category {
            background: var(--media-blue-soft);
            color: #1d4ed8;
        }

        .media-visibility {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 0.84rem;
        }

        .media-visibility.public { color: #047857; }
        .media-visibility.private { color: #64748b; }

        .media-date {
            font-weight: 700;
            color: var(--media-ink);
        }

        .media-date-sub,
        .media-status-updated,
        .media-status-note {
            font-size: 0.84rem;
            color: var(--media-muted);
        }

        .media-copy-btn {
            border-radius: 999px;
            padding: 0.3rem 0.78rem;
            border-color: #dbe4f0;
            color: #0f172a;
            font-size: 0.78rem;
            font-weight: 700;
            background: #fff;
        }

        .media-status-stack {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 235px;
        }

        .media-status-chip {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .media-status-chip.info {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .media-status-chip.success {
            background: #dcfce7;
            color: #15803d;
        }

        .media-status-chip.danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .media-status-chip.dark {
            background: #0f172a;
            color: #fff;
        }

        .media-status-error {
            color: #dc2626;
            font-size: 0.82rem;
            line-height: 1.4;
        }

        .media-inline-link {
            color: #dc2626;
            font-size: 0.84rem;
            font-weight: 700;
            text-decoration: none;
        }

        .media-inline-link:hover {
            text-decoration: underline;
        }

        .media-action-btn {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            border-color: var(--media-line);
            background: #fff;
        }

        .media-empty-state {
            text-align: center;
            padding: 56px 20px;
            border: 1px dashed #cbd5e1;
            border-radius: 24px;
            background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
        }

        .media-empty-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 18px;
            border-radius: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--media-primary-soft);
            color: var(--media-primary);
            font-size: 1.5rem;
        }

        @media (max-width: 991.98px) {
            .media-toolbar {
                flex-direction: column;
            }

            .media-toolbar-badge {
                align-self: flex-start;
            }
        }

        @media (max-width: 767.98px) {
            .media-index-hero,
            .media-library-card {
                border-radius: 22px;
            }

            .media-stat-card,
            .media-filter-form {
                border-radius: 18px;
            }

            .media-main-cell {
                min-width: 220px;
            }

            .media-thumb,
            .media-audio-thumb {
                width: 88px;
                height: 66px;
            }
        }
    </style>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tr[data-href]');

            rows.forEach(row => {
                row.addEventListener('click', function(e) {
                    if (e.target.closest('.dropdown, .btn, a, button, form, input, label')) {
                        return;
                    }

                    window.location.href = this.dataset.href;
                });
            });

            document.querySelectorAll('[data-bulk-form]').forEach(form => {
                const selectAll = form.querySelector('[data-select-all]');
                const checkboxes = Array.from(form.querySelectorAll('[data-select-item]'));
                const countTarget = form.querySelector('[data-selected-count]');
                const submitButton = form.querySelector('[data-bulk-submit]');

                const updateState = () => {
                    const checked = checkboxes.filter(checkbox => checkbox.checked);

                    if (countTarget) {
                        countTarget.textContent = String(checked.length);
                    }

                    if (submitButton) {
                        submitButton.disabled = checked.length === 0;
                    }

                    if (selectAll) {
                        selectAll.checked = checked.length > 0 && checked.length === checkboxes.length;
                        selectAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
                    }

                    checkboxes.forEach(checkbox => {
                        const row = checkbox.closest('tr[data-href]');
                        if (row) {
                            row.classList.toggle('is-selected', checkbox.checked);
                        }
                    });
                };

                if (selectAll) {
                    selectAll.addEventListener('click', event => event.stopPropagation());
                    selectAll.addEventListener('change', function() {
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = selectAll.checked;
                        });
                        updateState();
                    });
                }

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('click', event => event.stopPropagation());
                    checkbox.addEventListener('change', updateState);
                });

                form.addEventListener('submit', function(event) {
                    const checked = checkboxes.filter(checkbox => checkbox.checked);
                    if (!checked.length) {
                        event.preventDefault();
                        return;
                    }

                    if (!window.confirm('Delete the selected media items? This action cannot be undone.')) {
                        event.preventDefault();
                    }
                });

                updateState();
            });

            document.querySelectorAll('[data-copy-text]').forEach(button => {
                button.addEventListener('click', async function(event) {
                    event.preventDefault();
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
