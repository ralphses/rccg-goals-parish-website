@props(['media', 'categories'])

<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h3 class="fw-bold mb-0">Media</h3>
                            <small class="text-muted">List of all media</small>
                        </div>
                        <a href="{{ route('dashboard.media.create') }}" class="btn btn-primary">Upload Media</a>
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

                        <!-- Search + Filter -->
                        <form method="GET" action="{{ route('dashboard.media.index') }}">
                            <div class="row mb-3 align-items-center">

                                <!-- Search -->
                                <div class="col-md-6 mb-2">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="Search media...">
                                </div>

                                <!-- Filter by Category -->
                                <div class="col-md-3 mb-2">
                                    <select name="category" class="form-select" onchange="this.form.submit()">
                                        <option value="">All Categories</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->value }}" {{ request('category') == $category->value ? 'selected' : '' }}>
                                                {{ ucwords(str_replace('_', ' ', $category->value)) }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title
                                            (A-Z)
                                        </option>
                                        <option value="title_desc"
                                            {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title
                                            (Z-A)</option>
                                    </select>
                                </div>

                            </div>
                        </form>

                        <!-- Media Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Uploaded By</th>
                                        <th>Created At</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($media as $index => $item)
                                        <tr data-href="{{ route('dashboard.media.show', $item->id) }}" style="cursor: pointer;">
                                            <td>{{ $media->firstItem() + $index }}</td>
                                            <td>
                                                <img src="{{ asset('storage/' . $item->file_path) }}" class="img-fluid" alt="{{ $item->title }}" style="max-height: 50px;">
                                            </td>
                                            <td>{{ $item->title }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $item->media_type->value)) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $item->category->value)) }}</span>
                                            </td>
                                            <td>
                                                @if ($item->mediable_type === 'App\\Models\\User')
                                                    {{ $item->mediable->name ?? 'N/A' }}
                                                @elseif ($item->mediable_type === 'App\\Models\\Testimony')
                                                    {{ $item->mediable->testifier_name ?? 'N/A' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $item->created_at->format('d M, Y') }}</td>
                                            <td onclick="event.stopPropagation();">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-bars"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('dashboard.media.show', $item->id) }}">
                                                                View
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('dashboard.media.edit', $item->id) }}">
                                                                Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="{{ route('dashboard.media.destroy', $item->id) }}" onsubmit="return confirm('Are you sure you want to delete this media?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
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
                                                No media found.
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
                                        @if ($media->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">Prev</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="{{ $media->previousPageUrl() . '&search=' . request('search') . '&category=' . request('category') }}">
                                                    Prev
                                                </a>
                                            </li>
                                        @endif

                                        {{-- Pages --}}
                                        @for ($i = 1; $i <= $media->lastPage(); $i++)
                                            @if ($i == $media->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $i }}</span>
                                                </li>
                                            @elseif($i == 1 || $i == $media->lastPage() || ($i >= $media->currentPage() - 2 && $i <= $media->currentPage() + 2))
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="{{ $media->url($i) . '&search=' . request('search') . '&category=' . request('category') }}">
                                                        {{ $i }}
                                                    </a>
                                                </li>
                                            @elseif($i == $media->currentPage() - 3 || $i == $media->currentPage() + 3)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                        @endfor

                                        {{-- Next --}}
                                        @if ($media->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="{{ $media->nextPageUrl() . '&search=' . request('search') . '&category=' . request('category') }}">
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
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tr[data-href]');
            rows.forEach(row => {
                row.addEventListener('click', function(e) {
                    // Check if the click is on the dropdown or any of its children
                    if (e.target.closest('.dropdown')) {
                        console.log("clieckd");
                        
                        return;
                    }
                    window.location.href = this.dataset.href;
                });
            });
        });
    </script>
@endpush