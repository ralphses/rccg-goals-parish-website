<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Departments</div>
                        @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                            <a href="{{ route('dashboard.departments.create') }}" class="btn btn-primary">Create
                                Department</a>
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
                        <form method="GET" action="{{ route('dashboard.departments.index') }}">
                            <div class="row mb-3 align-items-center">

                                <!-- Search -->
                                <div class="col-md-6 mb-2">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="Search departments...">
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

                        <!-- Departments Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">

                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($departments as $index => $department)
                                        <tr style="cursor: pointer;"
                                            onclick="window.location='{{ route('dashboard.departments.show', $department->id) }}'">

                                            <td>
                                                {{ $departments->firstItem() + $index }}
                                            </td>

                                            <td>{{ $department->name }}</td>

                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $department->status }}
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
                                                                href="{{ route('dashboard.departments.show', $department->id) }}">
                                                                View
                                                            </a>
                                                        </li>
                                                        @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('dashboard.departments.edit', $department->id) }}">
                                                                    Edit
                                                                </a>
                                                            </li>

                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>

                                                            <li>
                                                                <form method="POST"
                                                                    action="{{ route('dashboard.departments.destroy', $department) }}"
                                                                    onsubmit="return confirm('Are you sure you want to delete this department?')">
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
                                            <td colspan="4" class="text-center">
                                                No departments found
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
                                        @if ($departments->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">Prev</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="{{ $departments->previousPageUrl() . '&search=' . request('search') . '&sort=' . request('sort') }}">
                                                    Prev
                                                </a>
                                            </li>
                                        @endif

                                        {{-- Pages --}}
                                        @for ($i = 1; $i <= $departments->lastPage(); $i++)
                                            @if ($i == $departments->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $i }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="{{ $departments->url($i) . '&search=' . request('search') . '&sort=' . request('sort') }}">
                                                        {{ $i }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endfor

                                        {{-- Next --}}
                                        @if ($departments->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="{{ $departments->nextPageUrl() . '&search=' . request('search') . '&sort=' . request('sort') }}">
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