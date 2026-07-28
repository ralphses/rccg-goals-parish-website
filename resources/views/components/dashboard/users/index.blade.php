@include('components.dashboard.partials.listing-shell')

@php
    $items = $users->getCollection();
    $activeCount = $items->filter(fn ($user) => $user->status->value === 'active')->count();
    $adminCount = $items->filter(fn ($user) => in_array($user->role->value, ['admin', 'pastor'], true))->count();
    $neverLoggedInCount = $items->filter(fn ($user) => blank($user->last_login_at))->count();
@endphp

<div class="container">
    <div class="page-inner">
        <div class="listing-shell">
            <div class="listing-hero card">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="listing-eyebrow">Users Directory</span>
                            <h2 class="listing-title">Manage people, roles, access, and activity from one cleaner workspace.</h2>
                            <p class="listing-subtitle">See who is active, who still needs attention, and jump straight into profile actions without digging through a crowded table.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="listing-hero-actions">
                                <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary btn-lg listing-primary-btn">+ Create User</a>
                                <div class="listing-hero-note">
                                    <span class="dot"></span>
                                    {{ $users->total() }} total users in the system
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-xl-3 col-md-6">
                    <div class="listing-stat-card">
                        <div class="listing-stat-icon" style="background:#eef6ff;color:#1d4ed8;"><i class="fas fa-users"></i></div>
                        <div><div class="listing-stat-value">{{ $users->count() }}</div><div class="listing-stat-label">Users On This Page</div></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="listing-stat-card">
                        <div class="listing-stat-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-user-check"></i></div>
                        <div><div class="listing-stat-value">{{ $activeCount }}</div><div class="listing-stat-label">Active Accounts</div></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="listing-stat-card">
                        <div class="listing-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-user-shield"></i></div>
                        <div><div class="listing-stat-value">{{ $adminCount }}</div><div class="listing-stat-label">Leadership Roles</div></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="listing-stat-card">
                        <div class="listing-stat-icon" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-user-clock"></i></div>
                        <div><div class="listing-stat-value">{{ $neverLoggedInCount }}</div><div class="listing-stat-label">Never Logged In</div></div>
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

            <div class="card listing-library-card mt-4">
                <div class="card-body p-4">
                    <div class="listing-toolbar mb-4">
                        <div>
                            <h3 class="listing-toolbar-title">Users</h3>
                            <p class="listing-toolbar-subtitle mb-0">Search by name, scan role and status quickly, and move into profile management with fewer clicks.</p>
                        </div>
                        <div class="listing-toolbar-badge">Showing {{ $users->count() }} of {{ $users->total() }}</div>
                    </div>

                    <form method="GET" action="{{ route('dashboard.users.index') }}" class="listing-filter-form mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-7">
                                <label class="form-label listing-filter-label" for="users-search">Search</label>
                                <div class="listing-search-wrap">
                                    <i class="fas fa-search"></i>
                                    <input id="users-search" type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search users by name or email...">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label listing-filter-label" for="users-sort">Sort</label>
                                <select id="users-sort" name="sort" class="form-select">
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
                                        <a href="{{ route('dashboard.users.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>

                    @if ($users->count())
                        <form method="POST" action="{{ route('dashboard.users.bulk-destroy') }}" data-bulk-form>
                            @csrf
                            @method('DELETE')
                            <div class="listing-bulk-bar">
                                <div class="listing-bulk-summary"><strong><span data-selected-count>0</span></strong> user(s) selected on this page</div>
                                <div class="listing-bulk-actions">
                                    <button type="submit" class="btn btn-outline-danger" data-bulk-submit disabled>Delete Selected</button>
                                </div>
                            </div>
                        <div class="table-responsive listing-table-wrap">
                            <table class="table listing-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="listing-check-cell"><input type="checkbox" class="form-check-input listing-check-input" data-select-all></th>
                                        <th>User</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr class="listing-row" data-listing-row data-href="{{ route('dashboard.users.show', $user->id) }}">
                                            <td class="listing-check-cell" onclick="event.stopPropagation();">
                                                <input type="checkbox" name="selected_ids[]" value="{{ $user->id }}" class="form-check-input listing-check-input" data-select-item>
                                            </td>
                                            <td>
                                                <div class="listing-main-cell">
                                                    <img src="{{ $user->avatar_url ?? asset('assets/img/default-avatar.png') }}" alt="{{ $user->name }}" class="listing-thumb-avatar">
                                                    <div>
                                                        <div class="listing-main-title">{{ $user->name }}</div>
                                                        <div class="listing-main-meta">
                                                            <span>{{ $user->email }}</span>
                                                            @if ($user->phone)
                                                                <span>{{ $user->phone }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="listing-pill info">{{ ucwords(str_replace('_', ' ', $user->role->value)) }}</span></td>
                                            <td>
                                                @if ($user->status->value === 'active')
                                                    <span class="listing-pill success">Active</span>
                                                @elseif ($user->status->value === 'created')
                                                    <span class="listing-pill warning">Created</span>
                                                @else
                                                    <span class="listing-pill danger">Suspended</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="listing-date">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</div>
                                                <div class="listing-date-sub">{{ $user->created_at->format('d M, Y') }}</div>
                                            </td>
                                            <td class="text-end" onclick="event.stopPropagation();">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm listing-action-btn" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="{{ route('dashboard.users.show', $user->id) }}">View</a></li>
                                                        <li><a class="dropdown-item" href="{{ route('dashboard.users.edit', $user->id) }}">Edit</a></li>
                                                        <li><a class="dropdown-item" href="{{ route('dashboard.users.departments', $user->id) }}">Departments</a></li>
                                                        <li><a class="dropdown-item" href="https://wa.me/{{ $user->phone }}" target="_blank">Message on WhatsApp</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form method="POST" action="{{ route('dashboard.users.destroy', $user->id) }}" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="dropdown-item text-danger">Delete</button>
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
                        <div class="listing-empty-state">
                            <div class="listing-empty-icon"><i class="fas fa-users-slash"></i></div>
                            <h4 class="mb-2">No users matched this view</h4>
                            <p class="text-muted mb-3">Try a different search or create a new account to get started.</p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary">Create User</a>
                                <a href="{{ route('dashboard.users.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                            </div>
                        </div>
                    @endif

                    @if ($users->hasPages())
                        <div class="row mt-4 align-items-center">
                            <div class="col-lg-6 mb-3 mb-lg-0">
                                <div class="listing-pagination-summary">Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results</div>
                            </div>
                            <div class="col-lg-6 d-flex justify-content-lg-end justify-content-center">
                                <nav aria-label="Pagination">
                                    <ul class="pagination pagination-modern mb-0">
                                        @if ($users->onFirstPage())
                                            <li class="page-item disabled"><span class="page-link">Prev</span></li>
                                        @else
                                            <li class="page-item"><a class="page-link" href="{{ $users->previousPageUrl() . '&search=' . request('search') . '&sort=' . request('sort') }}">Prev</a></li>
                                        @endif
                                        @for ($i = 1; $i <= $users->lastPage(); $i++)
                                            @if ($i == $users->currentPage())
                                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                                            @elseif($i == 1 || $i == $users->lastPage() || ($i >= $users->currentPage() - 2 && $i <= $users->currentPage() + 2))
                                                <li class="page-item"><a class="page-link" href="{{ $users->url($i) . '&search=' . request('search') . '&sort=' . request('sort') }}">{{ $i }}</a></li>
                                            @elseif($i == $users->currentPage() - 3 || $i == $users->currentPage() + 3)
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            @endif
                                        @endfor
                                        @if ($users->hasMorePages())
                                            <li class="page-item"><a class="page-link" href="{{ $users->nextPageUrl() . '&search=' . request('search') . '&sort=' . request('sort') }}">Next</a></li>
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
