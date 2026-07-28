@include('components.dashboard.partials.listing-shell')

@php
    $items = $departments->getCollection();
    $activeCount = $items->filter(fn ($department) => strcasecmp((string) $department->status, 'active') === 0)->count();
    $inactiveCount = $departments->count() - $activeCount;
@endphp

<div class="container">
    <div class="page-inner">
        <div class="listing-shell">
            <div class="listing-hero card">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="listing-eyebrow">Departments Hub</span>
                            <h2 class="listing-title">Keep ministry departments organized, visible, and easy to manage.</h2>
                            <p class="listing-subtitle">Browse department records faster, spot status at a glance, and move from overview to editing without friction.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="listing-hero-actions">
                                @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                    <a href="{{ route('dashboard.departments.create') }}" class="btn btn-primary btn-lg listing-primary-btn">Create Department</a>
                                @endif
                                <div class="listing-hero-note">
                                    <span class="dot"></span>
                                    {{ $departments->total() }} total departments tracked
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-xl-4 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#eef6ff;color:#1d4ed8;"><i class="fas fa-sitemap"></i></div><div><div class="listing-stat-value">{{ $departments->count() }}</div><div class="listing-stat-label">Departments On This Page</div></div></div></div>
                <div class="col-xl-4 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-check-circle"></i></div><div><div class="listing-stat-value">{{ $activeCount }}</div><div class="listing-stat-label">Active Departments</div></div></div></div>
                <div class="col-xl-4 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-pause-circle"></i></div><div><div class="listing-stat-value">{{ $inactiveCount }}</div><div class="listing-stat-label">Other Statuses</div></div></div></div>
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
                            <h3 class="listing-toolbar-title">Departments</h3>
                            <p class="listing-toolbar-subtitle mb-0">Search by name, scan status quickly, and step into each department profile from a cleaner overview.</p>
                        </div>
                        <div class="listing-toolbar-badge">Showing {{ $departments->count() }} of {{ $departments->total() }}</div>
                    </div>

                    <form method="GET" action="{{ route('dashboard.departments.index') }}" class="listing-filter-form mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-7">
                                <label class="form-label listing-filter-label" for="departments-search">Search</label>
                                <div class="listing-search-wrap">
                                    <i class="fas fa-search"></i>
                                    <input id="departments-search" type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search departments...">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label listing-filter-label" for="departments-sort">Sort</label>
                                <select id="departments-sort" name="sort" class="form-select">
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
                                        <a href="{{ route('dashboard.departments.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>

                    @if ($departments->count())
                        <form method="POST" action="{{ route('dashboard.departments.bulk-destroy') }}" data-bulk-form>
                            @csrf
                            @method('DELETE')
                            <div class="listing-bulk-bar">
                                <div class="listing-bulk-summary"><strong><span data-selected-count>0</span></strong> department(s) selected on this page</div>
                                <div class="listing-bulk-actions">
                                    <button type="submit" class="btn btn-outline-danger" data-bulk-submit disabled>Delete Selected</button>
                                </div>
                            </div>
                        <div class="table-responsive listing-table-wrap">
                            <table class="table listing-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="listing-check-cell"><input type="checkbox" class="form-check-input listing-check-input" data-select-all></th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($departments as $department)
                                        <tr class="listing-row" data-listing-row data-href="{{ route('dashboard.departments.show', $department->id) }}">
                                            <td class="listing-check-cell" onclick="event.stopPropagation();">
                                                <input type="checkbox" name="selected_ids[]" value="{{ $department->id }}" class="form-check-input listing-check-input" data-select-item>
                                            </td>
                                            <td>
                                                <div class="listing-main-cell">
                                                    <div class="listing-thumb-icon"><i class="fas fa-users-cog"></i></div>
                                                    <div>
                                                        <div class="listing-main-title">{{ $department->name }}</div>
                                                        <div class="listing-main-meta">
                                                            <span>#{{ $department->id }}</span>
                                                            <span>Created {{ $department->created_at->format('d M, Y') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="listing-pill {{ strcasecmp((string) $department->status, 'active') === 0 ? 'success' : 'info' }}">
                                                    {{ ucwords((string) $department->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end" onclick="event.stopPropagation();">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm listing-action-btn" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="{{ route('dashboard.departments.show', $department->id) }}">View</a></li>
                                                        @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                                            <li><a class="dropdown-item" href="{{ route('dashboard.departments.edit', $department->id) }}">Edit</a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form method="POST" action="{{ route('dashboard.departments.destroy', $department) }}" onsubmit="return confirm('Are you sure you want to delete this department?')">
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
                            <div class="listing-empty-icon"><i class="fas fa-sitemap"></i></div>
                            <h4 class="mb-2">No departments matched this view</h4>
                            <p class="text-muted mb-3">Try a different search or create a new department to keep the structure growing.</p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                    <a href="{{ route('dashboard.departments.create') }}" class="btn btn-primary">Create Department</a>
                                @endif
                                <a href="{{ route('dashboard.departments.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                            </div>
                        </div>
                    @endif

                    @if ($departments->hasPages())
                        <div class="row mt-4 align-items-center">
                            <div class="col-lg-6 mb-3 mb-lg-0"><div class="listing-pagination-summary">Showing {{ $departments->firstItem() }} to {{ $departments->lastItem() }} of {{ $departments->total() }} results</div></div>
                            <div class="col-lg-6 d-flex justify-content-lg-end justify-content-center">
                                <nav aria-label="Pagination">
                                    <ul class="pagination pagination-modern mb-0">
                                        @if ($departments->onFirstPage())
                                            <li class="page-item disabled"><span class="page-link">Prev</span></li>
                                        @else
                                            <li class="page-item"><a class="page-link" href="{{ $departments->previousPageUrl() . '&search=' . request('search') . '&sort=' . request('sort') }}">Prev</a></li>
                                        @endif
                                        @for ($i = 1; $i <= $departments->lastPage(); $i++)
                                            @if ($i == $departments->currentPage())
                                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                                            @elseif($i == 1 || $i == $departments->lastPage() || ($i >= $departments->currentPage() - 2 && $i <= $departments->currentPage() + 2))
                                                <li class="page-item"><a class="page-link" href="{{ $departments->url($i) . '&search=' . request('search') . '&sort=' . request('sort') }}">{{ $i }}</a></li>
                                            @elseif($i == $departments->currentPage() - 3 || $i == $departments->currentPage() + 3)
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            @endif
                                        @endfor
                                        @if ($departments->hasMorePages())
                                            <li class="page-item"><a class="page-link" href="{{ $departments->nextPageUrl() . '&search=' . request('search') . '&sort=' . request('sort') }}">Next</a></li>
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
