<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h3 class="fw-bold mb-0">Events</h3>
                            <small class="text-muted">List of all events</small>
                        </div>
                        @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                            <a href="{{ route('dashboard.events.create') }}" class="btn btn-primary">Create Event</a>
                        @endif
                    </div>
                    {{-- Session Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card-body">

                        <!-- Search + Sort -->
                        <form method="GET" action="{{ route('dashboard.events.index') }}">
                            <div class="row mb-3 align-items-center">

                                <!-- Search -->
                                <div class="col-md-6 mb-2">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="Search events...">
                                </div>

                                <!-- Sort -->
                                <div class="col-md-3 mb-2 ms-auto">
                                    <select name="sort" class="form-select" onchange="this.form.submit()">
                                        <option value="">Sort By</option>
                                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>
                                            Latest
                                        </option>
                                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                            Oldest
                                        </option>
                                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name
                                            (A-Z)
                                        </option>
                                        <option value="name_desc"
                                            {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name
                                            (Z-A)</option>
                                    </select>
                                </div>

                            </div>
                        </form>

                        <!-- Events Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">

                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($events as $index => $event)
                                        <tr style="cursor: pointer;"
                                            onclick="window.location='{{ route('dashboard.events.show', $event->id) }}'">

                                            <td>
                                                {{ $events->firstItem() + $index }}
                                            </td>

                                            <td>
                                                <img src="{{ $event->image ? asset('storage/' . $event->image) : asset('assets/img/default-image.png') }}"
                                                    width="40" height="40" class="rounded-circle">
                                            </td>

                                            <td>{{ $event->title }}</td>

                                            <td>{{ $event->event_date->format('d M, Y') }}</td>

                                            <td>{{ $event->location }}</td>

                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $event->status }}
                                                </span>
                                            </td>

                                            <!-- Actions -->
                                            <td onclick="event.stopPropagation();">

                                                <div class="dropdown">

                                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fas fa-bars"></i>
                                                    </button>

                                                    <ul class="dropdown-menu dropdown-menu-end">

                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('dashboard.events.show', $event->id) }}">
                                                                View
                                                            </a>
                                                        </li>
                                                        @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('dashboard.events.edit', $event->id) }}">
                                                                    Edit
                                                                </a>
                                                            </li>

                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>

                                                            <li>
                                                                <form method="POST"
                                                                    action="{{ route('dashboard.events.destroy', $event->id) }}"
                                                                    onsubmit="return confirm('Are you sure you want to delete this event?')">
                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <button class="dropdown-item text-danger">
                                                                        Delete
                                                                    </button>

                                                                </form>
                                                            </li>
                                                        @endif
                                                    </ul>

                                                </div>

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="7" class="text-center">
                                                No events found
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="row mb-4 align-items-center">
                            <div class="col-12 d-flex justify-content-center">
                                <nav aria-label="Pagination">
                                    <ul class="pagination pagination-modern mb-0">

                                        {{-- Prev --}}
                                        @if ($events->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">Prev</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="{{ $events->previousPageUrl() . '&search=' . request('search') . '&sort=' . request('sort') }}">
                                                    Prev
                                                </a>
                                            </li>
                                        @endif

                                        {{-- Pages --}}
                                        @for ($i = 1; $i <= $events->lastPage(); $i++)
                                            @if ($i == $events->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $i }}</span>
                                                </li>
                                            @elseif($i == 1 || $i == $events->lastPage() || ($i >= $events->currentPage() - 2 && $i <= $events->currentPage() + 2))
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="{{ $events->url($i) . '&search=' . request('search') . '&sort=' . request('sort') }}">
                                                        {{ $i }}
                                                    </a>
                                                </li>
                                            @elseif($i == $events->currentPage() - 3 || $i == $events->currentPage() + 3)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                        @endfor

                                        {{-- Next --}}
                                        @if ($events->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="{{ $events->nextPageUrl() . '&search=' . request('search') . '&sort=' . request('sort') }}">
                                                    Next
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link">Next</span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>