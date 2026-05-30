<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Users</div>

                        <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary">
                            + Create User
                        </a>
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
                        <form method="GET" action="{{ route('dashboard.users.index') }}">
                            <div class="row mb-3 align-items-center">

                                <!-- Search -->
                                <div class="col-md-6 mb-2">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="Search users...">
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

                        <!-- Users Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">

                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Avatar</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($users as $index => $user)
                                        <tr style="cursor: pointer;"
                                            onclick="window.location='{{ route('dashboard.users.show', $user->id) }}'">

                                            <td>
                                                {{ $users->firstItem() + $index }}
                                            </td>

                                            <td>
                                                <img src="{{ $user->avatar ?? asset('assets/img/default-avatar.png') }}"
                                                    width="40" height="40" class="rounded-circle">
                                            </td>

                                            <td>{{ $user->name }}</td>

                                            <td>{{ $user->email }}</td>

                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $user->role->value }}
                                                </span>
                                            </td>

                                            <td>
                                                @if ($user->status->value === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($user->status->value === 'created')
                                                    <span class="badge bg-warning">Created</span>
                                                @else
                                                    <span class="badge bg-danger">Suspended</span>
                                                @endif
                                            </td>

                                            <td>
                                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
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
                                                                href="{{ route('dashboard.users.show', $user->id) }}">
                                                                View
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('dashboard.users.edit', $user->id) }}">
                                                                Edit
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('dashboard.users.departments', $user->id) }}">
                                                                Departments
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="https://wa.me/{{ $user->phone }}"
                                                                target="_blank">
                                                                Message on WhatsApp
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>

                                                        <li>
                                                            <form method="POST"
                                                                action="{{ route('dashboard.users.destroy', $user->id) }}"
                                                                onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button class="dropdown-item text-danger">
                                                                    Delete
                                                                </button>

                                                            </form>
                                                        </li>

                                                    </ul>

                                                </div>

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="8" class="text-center">
                                                No users found
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>

                <!-- Pagination -->
                <div class="row mb-4 align-items-center">
                    <div class="col-12 d-flex justify-content-center">
                        <nav aria-label="Pagination">
                            <ul class="pagination pagination-modern mb-0">

                                {{-- Prev --}}
                                @if ($users->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">Prev</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $users->previousPageUrl() . '&search=' . request('search') . '&sort=' . request('sort') }}">
                                            Prev
                                        </a>
                                    </li>
                                @endif

                                {{-- Pages --}}
                                @for ($i = 1; $i <= $users->lastPage(); $i++)
                                    @if ($i == $users->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $i }}</span>
                                        </li>
                                    @elseif($i == 1 || $i == $users->lastPage() || ($i >= $users->currentPage() - 2 && $i <= $users->currentPage() + 2))
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="{{ $users->url($i) . '&search=' . request('search') . '&sort=' . request('sort') }}">
                                                {{ $i }}
                                            </a>
                                        </li>
                                    @elseif($i == $users->currentPage() - 3 || $i == $users->currentPage() + 3)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                @endfor

                                {{-- Next --}}
                                @if ($users->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $users->nextPageUrl() . '&search=' . request('search') . '&sort=' . request('sort') }}">
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