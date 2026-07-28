@include('components.dashboard.partials.listing-shell')

@php
    $items = $events->getCollection();
    $upcomingCount = $items->filter(fn ($event) => $event->event_date && $event->event_date->isFuture())->count();
    $pastCount = $items->filter(fn ($event) => $event->event_date && $event->event_date->isPast())->count();
@endphp

<div class="container">
    <div class="page-inner">
        <div class="listing-shell">
            <div class="listing-hero card">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="listing-eyebrow">Events Calendar</span>
                            <h2 class="listing-title">Plan, track, and review church events from a calmer admin view.</h2>
                            <p class="listing-subtitle">Keep event records easier to scan with clearer dates, locations, and status details right where the team needs them.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="listing-hero-actions">
                                @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                    <a href="{{ route('dashboard.events.create') }}" class="btn btn-primary btn-lg listing-primary-btn">Create Event</a>
                                @endif
                                <div class="listing-hero-note"><span class="dot"></span>{{ $events->total() }} total events recorded</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-xl-4 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#eef6ff;color:#1d4ed8;"><i class="fas fa-calendar-alt"></i></div><div><div class="listing-stat-value">{{ $events->count() }}</div><div class="listing-stat-label">Events On This Page</div></div></div></div>
                <div class="col-xl-4 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-flag-checkered"></i></div><div><div class="listing-stat-value">{{ $upcomingCount }}</div><div class="listing-stat-label">Upcoming Events</div></div></div></div>
                <div class="col-xl-4 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-history"></i></div><div><div class="listing-stat-value">{{ $pastCount }}</div><div class="listing-stat-label">Past Events</div></div></div></div>
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
                            <h3 class="listing-toolbar-title">Events</h3>
                            <p class="listing-toolbar-subtitle mb-0">Search by title, review location and timing quickly, and move into event details with less friction.</p>
                        </div>
                        <div class="listing-toolbar-badge">Showing {{ $events->count() }} of {{ $events->total() }}</div>
                    </div>

                    <form method="GET" action="{{ route('dashboard.events.index') }}" class="listing-filter-form mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-7">
                                <label class="form-label listing-filter-label" for="events-search">Search</label>
                                <div class="listing-search-wrap">
                                    <i class="fas fa-search"></i>
                                    <input id="events-search" type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search events...">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label listing-filter-label" for="events-sort">Sort</label>
                                <select id="events-sort" name="sort" class="form-select">
                                    <option value="">Latest First</option>
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <div class="listing-filter-actions">
                                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                                    @if (request()->filled('search') || request()->filled('sort'))
                                        <a href="{{ route('dashboard.events.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>

                    @if ($events->count())
                        <form method="POST" action="{{ route('dashboard.events.bulk-destroy') }}" data-bulk-form>
                            @csrf
                            @method('DELETE')
                            <div class="listing-bulk-bar">
                                <div class="listing-bulk-summary"><strong><span data-selected-count>0</span></strong> event(s) selected on this page</div>
                                <div class="listing-bulk-actions">
                                    <button type="submit" class="btn btn-outline-danger" data-bulk-submit disabled>Delete Selected</button>
                                </div>
                            </div>
                        <div class="table-responsive listing-table-wrap">
                            <table class="table listing-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="listing-check-cell"><input type="checkbox" class="form-check-input listing-check-input" data-select-all></th>
                                        <th>Event</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($events as $event)
                                        <tr class="listing-row" data-listing-row data-href="{{ route('dashboard.events.show', $event->id) }}">
                                            <td class="listing-check-cell" onclick="event.stopPropagation();">
                                                <input type="checkbox" name="selected_ids[]" value="{{ $event->id }}" class="form-check-input listing-check-input" data-select-item>
                                            </td>
                                            <td>
                                                <div class="listing-main-cell">
                                                    <img src="{{ $event->image_url ?? asset('assets/img/default-image.png') }}" alt="{{ $event->title }}" class="listing-thumb">
                                                    <div>
                                                        <div class="listing-main-title">{{ $event->title }}</div>
                                                        <div class="listing-main-meta">
                                                            <span>#{{ $event->id }}</span>
                                                            <span>Created {{ $event->created_at->format('d M, Y') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="listing-date">{{ $event->event_date->format('d M, Y') }}</div>
                                                <div class="listing-date-sub">{{ $event->event_date->isFuture() ? 'Upcoming' : 'Past event' }}</div>
                                            </td>
                                            <td>{{ $event->location }}</td>
                                            <td><span class="listing-pill info">{{ ucwords((string) $event->status) }}</span></td>
                                            <td class="text-end" onclick="event.stopPropagation();">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm listing-action-btn" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="{{ route('dashboard.events.show', $event->id) }}">View</a></li>
                                                        @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                                            <li><a class="dropdown-item" href="{{ route('dashboard.events.edit', $event->id) }}">Edit</a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form method="POST" action="{{ route('dashboard.events.destroy', $event->id) }}" onsubmit="return confirm('Are you sure you want to delete this event?')">
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
                            <div class="listing-empty-icon"><i class="fas fa-calendar-times"></i></div>
                            <h4 class="mb-2">No events matched this view</h4>
                            <p class="text-muted mb-3">Try a different search or add a fresh event to keep the calendar current.</p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                    <a href="{{ route('dashboard.events.create') }}" class="btn btn-primary">Create Event</a>
                                @endif
                                <a href="{{ route('dashboard.events.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                            </div>
                        </div>
                    @endif

                    @if ($events->hasPages())
                        <div class="row mt-4 align-items-center">
                            <div class="col-lg-6 mb-3 mb-lg-0"><div class="listing-pagination-summary">Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} results</div></div>
                            <div class="col-lg-6 d-flex justify-content-lg-end justify-content-center">
                                <nav aria-label="Pagination">
                                    <ul class="pagination pagination-modern mb-0">
                                        @if ($events->onFirstPage())
                                            <li class="page-item disabled"><span class="page-link">Prev</span></li>
                                        @else
                                            <li class="page-item"><a class="page-link" href="{{ $events->previousPageUrl() . '&search=' . request('search') . '&sort=' . request('sort') }}">Prev</a></li>
                                        @endif
                                        @for ($i = 1; $i <= $events->lastPage(); $i++)
                                            @if ($i == $events->currentPage())
                                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                                            @elseif($i == 1 || $i == $events->lastPage() || ($i >= $events->currentPage() - 2 && $i <= $events->currentPage() + 2))
                                                <li class="page-item"><a class="page-link" href="{{ $events->url($i) . '&search=' . request('search') . '&sort=' . request('sort') }}">{{ $i }}</a></li>
                                            @elseif($i == $events->currentPage() - 3 || $i == $events->currentPage() + 3)
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            @endif
                                        @endfor
                                        @if ($events->hasMorePages())
                                            <li class="page-item"><a class="page-link" href="{{ $events->nextPageUrl() . '&search=' . request('search') . '&sort=' . request('sort') }}">Next</a></li>
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
