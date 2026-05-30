@props(['announcements'])

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Announcements</h4>
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
                <a href="{{ route('dashboard.announcements.index') }}">Announcements</a>
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
                        <h4 class="card-title">All Announcements</h4>
                        <a href="{{ route('dashboard.announcements.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add New
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="announcements-table" class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Creator</th>
                                    <th>Service Date</th>
                                    <th>Frequency</th>
                                    <th>Approved</th>
                                    <th>Created At</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($announcements as $index => $announcement)
                                    <tr data-href="{{ route('dashboard.announcements.show', $announcement->id) }}" style="cursor: pointer;">
                                        <td>{{ $announcements->firstItem() + $index }}</td>
                                        <td>{{ $announcement->title }}</td>
                                        <td>{{ $announcement->creator->name }}</td>
                                        <td>{{ $announcement->service_date->format('d M, Y') }}</td>
                                        <td>{{ $announcement->frequency->name }}</td>
                                        <td>
                                            @if ($announcement->is_approved)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>
                                        <td>{{ $announcement->created_at->format('d M, Y') }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-bars"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('dashboard.announcements.show', $announcement->id) }}">
                                                            View
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('dashboard.announcements.edit', $announcement->id) }}">
                                                            Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('dashboard.announcements.destroy', $announcement->id) }}" onsubmit="return confirm('Are you sure you want to delete this announcement?')">
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
                                            No announcements found.
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
                                        @if ($announcements->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">Prev</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="{{ $announcements->previousPageUrl() }}">
                                                    Prev
                                                </a>
                                            </li>
                                        @endif

                                        {{-- Pages --}}
                                        @for ($i = 1; $i <= $announcements->lastPage(); $i++)
                                            @if ($i == $announcements->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $i }}</span>
                                                </li>
                                            @elseif($i == 1 || $i == $announcements->lastPage() || ($i >= $announcements->currentPage() - 2 && $i <= $announcements->currentPage() + 2))
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="{{ $announcements->url($i) }}">
                                                        {{ $i }}
                                                    </a>
                                                </li>
                                            @elseif($i == $announcements->currentPage() - 3 || $i == $announcements->currentPage() + 3)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                        @endfor

                                        {{-- Next --}}
                                        @if ($announcements->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="{{ $announcements->nextPageUrl() }}">
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
        document.querySelectorAll('#announcements-table tbody tr[data-href]').forEach(row => {
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