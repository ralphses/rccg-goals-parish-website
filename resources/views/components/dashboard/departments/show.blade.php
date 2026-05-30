<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">View Department</div>
                        <div>
                              @if (auth()->user()->isAdmin() || auth()->user()->isPastor())

                            <a href="{{ route('dashboard.departments.edit', $department->id) }}"
                                class="btn btn-primary">Edit</a>
                                @endif
                            <a href="{{ route('dashboard.departments.index') }}" class="btn btn-light">Back</a>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <!-- Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name</label>
                                <p class="form-control">{{ $department->name }}</p>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <p class="form-control">{{ $department->status }}</p>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <div class="form-control" style="height: auto;">
                                    {!! $department->description !!}
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>