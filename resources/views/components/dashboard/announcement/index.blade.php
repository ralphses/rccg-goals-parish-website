@props(['announcements'])

@include('components.dashboard.partials.listing-shell')

@php
    $items = $announcements->getCollection();
    $approvedCount = $items->where('is_approved', true)->count();
    $pendingCount = $announcements->count() - $approvedCount;
    $recurringCount = $items->filter(fn ($announcement) => strtolower((string) $announcement->frequency->name) !== 'once')->count();
@endphp

<div class="container">
    <div class="page-inner">
        <div class="listing-shell">
            <div class="listing-hero card">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="listing-eyebrow">Announcements Desk</span>
                            <h2 class="listing-title">Keep church announcements easier to review, approve, and schedule.</h2>
                            <p class="listing-subtitle">See who created each notice, when it is meant to run, and what still needs approval from one cleaner screen.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="listing-hero-actions">
                                <a href="{{ route('dashboard.announcements.create') }}" class="btn btn-primary btn-lg listing-primary-btn"><i class="fa fa-plus me-2"></i>Add New</a>
                                <div class="listing-hero-note"><span class="dot"></span>{{ $announcements->total() }} total announcements recorded</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-xl-4 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#eef6ff;color:#1d4ed8;"><i class="fas fa-bullhorn"></i></div><div><div class="listing-stat-value">{{ $announcements->count() }}</div><div class="listing-stat-label">Announcements On This Page</div></div></div></div>
                <div class="col-xl-4 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-check-circle"></i></div><div><div class="listing-stat-value">{{ $approvedCount }}</div><div class="listing-stat-label">Approved</div></div></div></div>
                <div class="col-xl-4 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-redo"></i></div><div><div class="listing-stat-value">{{ $recurringCount }}</div><div class="listing-stat-label">Recurring</div></div></div></div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            @endif

            <div class="card listing-library-card mt-4">
                <div class="card-body p-4">
                    <div class="listing-toolbar mb-4">
                        <div>
                            <h3 class="listing-toolbar-title">All Announcements</h3>
                            <p class="listing-toolbar-subtitle mb-0">Review message ownership, service dates, recurrence, and approval state without wading through a dense table.</p>
                        </div>
                        <div class="listing-toolbar-badge">Showing {{ $announcements->count() }} of {{ $announcements->total() }}</div>
                    </div>

                    @if ($announcements->count())
                        <form method="POST" action="{{ route('dashboard.announcements.bulk-destroy') }}" data-bulk-form>
                            @csrf
                            @method('DELETE')
                            <div class="listing-bulk-bar">
                                <div class="listing-bulk-summary"><strong><span data-selected-count>0</span></strong> announcement(s) selected on this page</div>
                                <div class="listing-bulk-actions">
                                    <button type="submit" class="btn btn-outline-danger" data-bulk-submit disabled>Delete Selected</button>
                                </div>
                            </div>
                        <div class="table-responsive listing-table-wrap">
                            <table class="table listing-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="listing-check-cell"><input type="checkbox" class="form-check-input listing-check-input" data-select-all></th>
                                        <th>Announcement</th>
                                        <th>Service Date</th>
                                        <th>Frequency</th>
                                        <th>Approval</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($announcements as $announcement)
                                        <tr class="listing-row" data-listing-row data-href="{{ route('dashboard.announcements.show', $announcement->id) }}">
                                            <td class="listing-check-cell" onclick="event.stopPropagation();">
                                                <input type="checkbox" name="selected_ids[]" value="{{ $announcement->id }}" class="form-check-input listing-check-input" data-select-item>
                                            </td>
                                            <td>
                                                <div class="listing-main-cell">
                                                    <div class="listing-thumb-icon"><i class="fas fa-bullhorn"></i></div>
                                                    <div>
                                                        <div class="listing-main-title">{{ $announcement->title }}</div>
                                                        <div class="listing-main-meta">
                                                            <span>{{ $announcement->creator->name }}</span>
                                                            <span>#{{ $announcement->id }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="listing-date">{{ $announcement->service_date->format('d M, Y') }}</div>
                                                <div class="listing-date-sub">{{ $announcement->created_at->format('d M, Y') }}</div>
                                            </td>
                                            <td><span class="listing-pill info">{{ $announcement->frequency->name }}</span></td>
                                            <td><span class="listing-pill {{ $announcement->is_approved ? 'success' : 'warning' }}">{{ $announcement->is_approved ? 'Approved' : 'Pending' }}</span></td>
                                            <td class="text-end" onclick="event.stopPropagation();">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm listing-action-btn" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="{{ route('dashboard.announcements.show', $announcement->id) }}">View</a></li>
                                                        <li><a class="dropdown-item" href="{{ route('dashboard.announcements.edit', $announcement->id) }}">Edit</a></li>
                                                        <li>
                                                            <form method="POST" action="{{ route('dashboard.announcements.destroy', $announcement->id) }}" onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">Delete</button>
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
                            <div class="listing-empty-icon"><i class="fas fa-bullhorn"></i></div>
                            <h4 class="mb-2">No announcements available yet</h4>
                            <p class="text-muted mb-3">Add a new announcement to start building the communication queue.</p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <a href="{{ route('dashboard.announcements.create') }}" class="btn btn-primary">Add Announcement</a>
                            </div>
                        </div>
                    @endif

                    @if ($announcements->hasPages())
                        <div class="row mt-4 align-items-center">
                            <div class="col-lg-6 mb-3 mb-lg-0"><div class="listing-pagination-summary">Showing {{ $announcements->firstItem() }} to {{ $announcements->lastItem() }} of {{ $announcements->total() }} results</div></div>
                            <div class="col-lg-6 d-flex justify-content-lg-end justify-content-center">
                                <nav aria-label="Pagination">
                                    <ul class="pagination pagination-modern mb-0">
                                        @if ($announcements->onFirstPage())
                                            <li class="page-item disabled"><span class="page-link">Prev</span></li>
                                        @else
                                            <li class="page-item"><a class="page-link" href="{{ $announcements->previousPageUrl() }}">Prev</a></li>
                                        @endif
                                        @for ($i = 1; $i <= $announcements->lastPage(); $i++)
                                            @if ($i == $announcements->currentPage())
                                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                                            @elseif($i == 1 || $i == $announcements->lastPage() || ($i >= $announcements->currentPage() - 2 && $i <= $announcements->currentPage() + 2))
                                                <li class="page-item"><a class="page-link" href="{{ $announcements->url($i) }}">{{ $i }}</a></li>
                                            @elseif($i == $announcements->currentPage() - 3 || $i == $announcements->currentPage() + 3)
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            @endif
                                        @endfor
                                        @if ($announcements->hasMorePages())
                                            <li class="page-item"><a class="page-link" href="{{ $announcements->nextPageUrl() }}">Next</a></li>
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
