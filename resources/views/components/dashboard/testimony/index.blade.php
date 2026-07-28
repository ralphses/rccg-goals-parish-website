@props(['testimonies'])

@include('components.dashboard.partials.listing-shell')

@php
    $items = $testimonies->getCollection();
    $approvedCount = $items->where('is_approved', true)->count();
    $featuredCount = $items->where('is_featured', true)->count();
    $pendingCount = $testimonies->count() - $approvedCount;
@endphp

<div class="container">
    <div class="page-inner">
        <div class="listing-shell">
            <div class="listing-hero card">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="listing-eyebrow">Testimonies Desk</span>
                            <h2 class="listing-title">Review, organize, and publish faith stories from a clearer queue.</h2>
                            <p class="listing-subtitle">Keep approvals, featured stories, and timeline visibility easier to manage for pastors and media teams.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="listing-hero-actions">
                                <a href="{{ route('dashboard.testimonies.create') }}" class="btn btn-primary btn-lg listing-primary-btn"><i class="fa fa-plus me-2"></i>Add New</a>
                                <div class="listing-hero-note"><span class="dot"></span>{{ $testimonies->total() }} total testimonies submitted</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-xl-4 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#eef6ff;color:#1d4ed8;"><i class="fas fa-comment-dots"></i></div><div><div class="listing-stat-value">{{ $testimonies->count() }}</div><div class="listing-stat-label">Testimonies On This Page</div></div></div></div>
                <div class="col-xl-4 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-check-circle"></i></div><div><div class="listing-stat-value">{{ $approvedCount }}</div><div class="listing-stat-label">Approved</div></div></div></div>
                <div class="col-xl-4 col-md-6"><div class="listing-stat-card"><div class="listing-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-star"></i></div><div><div class="listing-stat-value">{{ $featuredCount }}</div><div class="listing-stat-label">Featured</div></div></div></div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            @endif

            <div class="card listing-library-card mt-4">
                <div class="card-body p-4">
                    <div class="listing-toolbar mb-4">
                        <div>
                            <h3 class="listing-toolbar-title">All Testimonies</h3>
                            <p class="listing-toolbar-subtitle mb-0">Review who submitted what, track approval state, and move into each story with less noise.</p>
                        </div>
                        <div class="listing-toolbar-badge">Showing {{ $testimonies->count() }} of {{ $testimonies->total() }}</div>
                    </div>

                    @if ($testimonies->count())
                        <form method="POST" action="{{ route('dashboard.testimonies.bulk-destroy') }}" data-bulk-form>
                            @csrf
                            @method('DELETE')
                            <div class="listing-bulk-bar">
                                <div class="listing-bulk-summary"><strong><span data-selected-count>0</span></strong> testimony item(s) selected on this page</div>
                                <div class="listing-bulk-actions">
                                    <button type="submit" class="btn btn-outline-danger" data-bulk-submit disabled>Delete Selected</button>
                                </div>
                            </div>
                        <div class="table-responsive listing-table-wrap">
                            <table class="table listing-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="listing-check-cell"><input type="checkbox" class="form-check-input listing-check-input" data-select-all></th>
                                        <th>Testimony</th>
                                        <th>Featured</th>
                                        <th>Approval</th>
                                        <th>Created</th>
                                        @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                            <th class="text-end">Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($testimonies as $testimony)
                                        <tr class="listing-row" data-listing-row data-href="{{ route('dashboard.testimonies.show', $testimony->id) }}">
                                            <td class="listing-check-cell" onclick="event.stopPropagation();">
                                                <input type="checkbox" name="selected_ids[]" value="{{ $testimony->id }}" class="form-check-input listing-check-input" data-select-item>
                                            </td>
                                            <td>
                                                <div class="listing-main-cell">
                                                    <div class="listing-thumb-icon"><i class="fas fa-hands-praying"></i></div>
                                                    <div>
                                                        <div class="listing-main-title">{{ $testimony->title }}</div>
                                                        <div class="listing-main-meta">
                                                            <span>{{ $testimony->testifier_name }}</span>
                                                            <span>#{{ $testimony->id }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="listing-pill {{ $testimony->is_featured ? 'success' : 'danger' }}">{{ $testimony->is_featured ? 'Featured' : 'Not Featured' }}</span></td>
                                            <td><span class="listing-pill {{ $testimony->is_approved ? 'success' : 'warning' }}">{{ $testimony->is_approved ? 'Approved' : 'Pending' }}</span></td>
                                            <td>
                                                <div class="listing-date">{{ $testimony->created_at->format('d M, Y') }}</div>
                                                <div class="listing-date-sub">{{ $testimony->created_at->diffForHumans() }}</div>
                                            </td>
                                            @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                                                <td class="text-end" onclick="event.stopPropagation();">
                                                    <div class="dropdown">
                                                        <button class="btn btn-light btn-sm listing-action-btn" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item" href="{{ route('dashboard.testimonies.show', $testimony->id) }}">View</a></li>
                                                            <li><a class="dropdown-item" href="{{ route('dashboard.testimonies.edit', $testimony->id) }}">Edit</a></li>
                                                            <li>
                                                                <form method="POST" action="{{ route('dashboard.testimonies.destroy', $testimony->id) }}" onsubmit="return confirm('Are you sure you want to delete this testimony?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        </form>
                    @else
                        <div class="listing-empty-state">
                            <div class="listing-empty-icon"><i class="fas fa-comment-slash"></i></div>
                            <h4 class="mb-2">No testimonies available yet</h4>
                            <p class="text-muted mb-3">Add a testimony to begin building the stories queue.</p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <a href="{{ route('dashboard.testimonies.create') }}" class="btn btn-primary">Add Testimony</a>
                            </div>
                        </div>
                    @endif

                    @if ($testimonies->hasPages())
                        <div class="row mt-4 align-items-center">
                            <div class="col-lg-6 mb-3 mb-lg-0"><div class="listing-pagination-summary">Showing {{ $testimonies->firstItem() }} to {{ $testimonies->lastItem() }} of {{ $testimonies->total() }} results</div></div>
                            <div class="col-lg-6 d-flex justify-content-lg-end justify-content-center">
                                <nav aria-label="Pagination">
                                    <ul class="pagination pagination-modern mb-0">
                                        @if ($testimonies->onFirstPage())
                                            <li class="page-item disabled"><span class="page-link">Prev</span></li>
                                        @else
                                            <li class="page-item"><a class="page-link" href="{{ $testimonies->previousPageUrl() }}">Prev</a></li>
                                        @endif
                                        @for ($i = 1; $i <= $testimonies->lastPage(); $i++)
                                            @if ($i == $testimonies->currentPage())
                                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                                            @elseif($i == 1 || $i == $testimonies->lastPage() || ($i >= $testimonies->currentPage() - 2 && $i <= $testimonies->currentPage() + 2))
                                                <li class="page-item"><a class="page-link" href="{{ $testimonies->url($i) }}">{{ $i }}</a></li>
                                            @elseif($i == $testimonies->currentPage() - 3 || $i == $testimonies->currentPage() + 3)
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            @endif
                                        @endfor
                                        @if ($testimonies->hasMorePages())
                                            <li class="page-item"><a class="page-link" href="{{ $testimonies->nextPageUrl() }}">Next</a></li>
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
