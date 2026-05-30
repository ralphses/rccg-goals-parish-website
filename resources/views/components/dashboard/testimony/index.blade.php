@props(['testimonies'])

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Testimonies</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('dashboard.testimonies.index') }}">Testimonies</a>
            </li>
        </ul>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">All Testimonies</h4>
                        <a href="{{ route('dashboard.testimonies.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add New
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="testimonies-table" class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Testifier</th>
                                    <th>Title</th>
                                    <th>Featured</th>
                                    <th>Approved</th>
                                    <th>Created At</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($testimonies as $index => $testimony)
                                    <tr data-href="{{ route('dashboard.testimonies.show', $testimony->id) }}" style="cursor: pointer;">
                                        <td>{{ $testimonies->firstItem() + $index }}</td>
                                        <td>{{ $testimony->testifier_name }}</td>
                                        <td>{{ $testimony->title }}</td>
                                        <td>
                                            @if ($testimony->is_featured)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($testimony->is_approved)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>
                                        <td>{{ $testimony->created_at->format('d M, Y') }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-bars"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('dashboard.testimonies.show', $testimony->id) }}">
                                                            View
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('dashboard.testimonies.edit', $testimony->id) }}">
                                                            Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('dashboard.testimonies.destroy', $testimony->id) }}" onsubmit="return confirm('Are you sure you want to delete this testimony?')">
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
                                        <td colspan="7" class="text-center">
                                            No testimonies found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <div class="row mb-4 align-items-center">
                            <div class="col-12 d-flex justify-content-center">
                                <nav aria-label="Pagination">
                                    <ul class="pagination pagination-modern mb-0">

                                        {{-- Prev --}}
                                        @if ($testimonies->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">Prev</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="{{ $testimonies->previousPageUrl() }}">
                                                    Prev
                                                </a>
                                            </li>
                                        @endif

                                        {{-- Pages --}}
                                        @for ($i = 1; $i <= $testimonies->lastPage(); $i++)
                                            @if ($i == $testimonies->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $i }}</span>
                                                </li>
                                            @elseif($i == 1 || $i == $testimonies->lastPage() || ($i >= $testimonies->currentPage() - 2 && $i <= $testimonies->currentPage() + 2))
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="{{ $testimonies->url($i) }}">
                                                        {{ $i }}
                                                    </a>
                                                </li>
                                            @elseif($i == $testimonies->currentPage() - 3 || $i == $testimonies->currentPage() + 3)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                        @endfor

                                        {{-- Next --}}
                                        @if ($testimonies->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="{{ $testimonies->nextPageUrl() }}">
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('#testimonies-table tbody tr[data-href]').forEach(row => {
            row.addEventListener('click', function (event) {
                // Stop propagation if the click is on a dropdown, button, or link inside the action cell
                if (event.target.closest('.dropdown, .btn, a')) {
                    return;
                }
                window.location.href = this.dataset.href;
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('#testimonies-table tbody tr[data-href]').forEach(row => {
            row.addEventListener('click', function (event) {
                // Stop propagation if the click is on a dropdown, button, or link inside the action cell
                if (event.target.closest('.dropdown, .btn, a')) {
                    return;
                }
                window.location.href = this.dataset.href;
            });
        });
    });
</script>