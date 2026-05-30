<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Edit Department</div>
                        <a href="{{ route('dashboard.departments.index') }}" class="btn btn-light">Back</a>
                    </div>

                    <div class="card-body">

                        <!-- Validation Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST"
                            action="{{ route('dashboard.departments.update', $department->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">

                                <!-- Name -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name"
                                        value="{{ old('name', $department->name) }}" class="form-control"
                                        required>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active"
                                            {{ old('status', $department->status) == 'active' ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option value="inactive"
                                            {{ old('status', $department->status) == 'inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                </div>

                                <!-- Description -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control"
                                        rows="5">{{ old('description', $department->description) }}</textarea>
                                </div>

                            </div>

                            <hr>

                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary">Update Department</button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>