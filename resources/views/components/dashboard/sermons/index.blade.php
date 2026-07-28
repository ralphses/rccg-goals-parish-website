@include('components.dashboard.partials.listing-shell')

@php
    $items = $sermons->getCollection();
    $publishedCount = $items->filter(fn ($sermon) => (string) $sermon->status === 'published')->count();
    $draftCount = $items->filter(fn ($sermon) => (string) $sermon->status === 'draft')->count();
    $archivedCount = $items->filter(fn ($sermon) => (string) $sermon->status === 'archived')->count();
@endphp

<div class="container">
    <div class="page-inner">
        <div class="listing-shell">
            <div class="listing-hero card">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="listing-eyebrow">Sermons Library</span>
                            <h2 class="listing-title">Manage sermon records with clearer status, speaker context, and faster navigation.</h2>
                            <p class="listing-subtitle">Keep the preaching archive easier to review by title, speaker, sermon date, and publication state.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="listing-hero-actions">
                                @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                    <a href="{{ route('dashboard.sermons.create') }}" class="btn btn-primary btn-lg listing-primary-btn">Create Sermon</a>
                                @endif
                                <div class="listing-hero-note"><span class="dot"></span>{{ $sermons->total() }} total sermons in archive</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-xl-3 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#eef6ff;color:#1d4ed8;"><i class="fas fa-bible"></i></div><div><div class="listing-stat-value">{{ $sermons->count() }}</div><div class="listing-stat-label">Sermons On This Page</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-check-double"></i></div><div><div class="listing-stat-value">{{ $publishedCount }}</div><div class="listing-stat-label">Published</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-edit"></i></div><div><div class="listing-stat-value">{{ $draftCount }}</div><div class="listing-stat-label">Drafts</div></div></div></div>
                <div class="col-xl-3 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#e2e8f0;color:#475569;"><i class="fas fa-archive"></i></div><div><div class="listing-stat-value">{{ $archivedCount }}</div><div class="listing-stat-label">Archived</div></div></div></div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            @endif

            <div class="card listing-library-card mt-4">
                <div class="card-body p-4">
                    <div class="listing-toolbar mb-4">
                        <div>
                            <h3 class="listing-toolbar-title">Sermons</h3>
                            <p class="listing-toolbar-subtitle mb-0">Search by title, scan speaker and sermon date, and move into attachments or publishing details more easily.</p>
                        </div>
                        <div class="listing-toolbar-badge">Showing {{ $sermons->count() }} of {{ $sermons->total() }}</div>
                    </div>

                    <form method="GET" action="{{ route('dashboard.sermons.index') }}" class="listing-filter-form mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-7">
                                <label class="form-label listing-filter-label" for="sermons-search">Search</label>
                                <div class="listing-search-wrap">
                                    <i class="fas fa-search"></i>
                                    <input id="sermons-search" type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search sermons by title...">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label listing-filter-label" for="sermons-sort">Sort</label>
                                <select id="sermons-sort" name="sort" class="form-select">
                                    <option value="">Latest First</option>
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title A-Z</option>
                                    <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title Z-A</option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <div class="listing-filter-actions">
                                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                                    @if (request()->filled('search') || request()->filled('sort'))
                                        <a href="{{ route('dashboard.sermons.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>

                    @if ($sermons->count())
                        <form method="POST" action="{{ route('dashboard.sermons.bulk-destroy') }}" data-bulk-form>
                            @csrf
                            @method('DELETE')
                            <div class="listing-bulk-bar">
                                <div class="listing-bulk-summary"><strong><span data-selected-count>0</span></strong> sermon(s) selected on this page</div>
                                <div class="listing-bulk-actions">
                                    <button type="submit" class="btn btn-outline-danger" data-bulk-submit disabled>Delete Selected</button>
                                </div>
                            </div>
                        <div class="table-responsive listing-table-wrap">
                            <table class="table listing-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="listing-check-cell"><input type="checkbox" class="form-check-input listing-check-input" data-select-all></th>
                                        <th>Sermon</th>
                                        <th>Speaker</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sermons as $sermon)
                                        @php
                                            $statusClass = match((string) $sermon->status) {
                                                'published' => 'success',
                                                'draft' => 'warning',
                                                'archived' => 'dark',
                                                default => 'info',
                                            };
                                        @endphp
                                        <tr class="listing-row" data-listing-row data-href="{{ route('dashboard.sermons.show', $sermon->id) }}">
                                            <td class="listing-check-cell" onclick="event.stopPropagation();">
                                                <input type="checkbox" name="selected_ids[]" value="{{ $sermon->id }}" class="form-check-input listing-check-input" data-select-item>
                                            </td>
                                            <td>
                                                <div class="listing-main-cell">
                                                    <div class="listing-thumb-icon"><i class="fas fa-microphone-alt"></i></div>
                                                    <div>
                                                        <div class="listing-main-title">{{ $sermon->title }}</div>
                                                        <div class="listing-main-meta">
                                                            <span>#{{ $sermon->id }}</span>
                                                            <span>Created {{ $sermon->created_at->format('d M, Y') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $sermon->speaker->name }}</td>
                                            <td>
                                                <div class="listing-date">{{ $sermon->sermon_date->format('d M, Y') }}</div>
                                                <div class="listing-date-sub">{{ $sermon->sermon_date->diffForHumans() }}</div>
                                            </td>
                                            <td><span class="listing-pill {{ $statusClass }}">{{ ucfirst((string) $sermon->status) }}</span></td>
                                            <td class="text-end" onclick="event.stopPropagation();">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm listing-action-btn" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="{{ route('dashboard.sermons.show', $sermon) }}">View</a></li>
                                                        @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                                            <li><a class="dropdown-item" href="{{ route('dashboard.sermons.edit', $sermon) }}">Edit</a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form method="POST" action="{{ route('dashboard.sermons.destroy', $sermon) }}" onsubmit="return confirm('Are you sure you want to delete this sermon?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="dropdown-item text-danger">Delete</button>
                                                                </form>
                                                            </li>
                                                        @endif
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
                        <div class="listing-empty-state">
                            <div class="listing-empty-icon"><i class="fas fa-bible"></i></div>
                            <h4 class="mb-2">No sermons matched this view</h4>
                            <p class="text-muted mb-3">Try a different search or add a new sermon to keep the archive growing.</p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                    <a href="{{ route('dashboard.sermons.create') }}" class="btn btn-primary">Create Sermon</a>
                                @endif
                                <a href="{{ route('dashboard.sermons.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                            </div>
                        </div>
                    @endif

                    @if ($sermons->hasPages())
                        <div class="row mt-4 align-items-center">
                            <div class="col-lg-6 mb-3 mb-lg-0"><div class="listing-pagination-summary">Showing {{ $sermons->firstItem() }} to {{ $sermons->lastItem() }} of {{ $sermons->total() }} results</div></div>
                            <div class="col-lg-6 d-flex justify-content-lg-end justify-content-center">
                                <nav aria-label="Pagination">
                                    <ul class="pagination pagination-modern mb-0">
                                        @if ($sermons->onFirstPage())
                                            <li class="page-item disabled"><span class="page-link">Prev</span></li>
                                        @else
                                            <li class="page-item"><a class="page-link" href="{{ $sermons->previousPageUrl() . '&search=' . request('search') . '&sort=' . request('sort') }}">Prev</a></li>
                                        @endif
                                        @for ($i = 1; $i <= $sermons->lastPage(); $i++)
                                            @if ($i == $sermons->currentPage())
                                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                                            @else
                                                <li class="page-item"><a class="page-link" href="{{ $sermons->url($i) . '&search=' . request('search') . '&sort=' . request('sort') }}">{{ $i }}</a></li>
                                            @endif
                                        @endfor
                                        @if ($sermons->hasMorePages())
                                            <li class="page-item"><a class="page-link" href="{{ $sermons->nextPageUrl() . '&search=' . request('search') . '&sort=' . request('sort') }}">Next</a></li>
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
