<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card"> <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">User Details</div>
                        <div> <a href="{{ route('dashboard.users.edit', $user->id) }}" class="btn btn-primary"> Edit </a>
                            <a href="{{ route('dashboard.users.index') }}" class="btn btn-light"> Back </a> </div>
                    </div>
                    <div class="card-body">
                        <div class="row"> <!-- Avatar -->
                            <div class="col-md-12 mb-4 text-center"> <img
                                    src="{{ $user->avatar ?? asset('assets/img/default-avatar.png') }}" width="120"
                                    height="120" class="rounded-circle"> </div> <!-- Name -->
                            <div class="col-md-6 mb-3"> <label class="form-label">Name</label>
                                <p class="form-control">{{ $user->name }}</p>
                            </div> <!-- Email -->
                            <div class="col-md-6 mb-3"> <label class="form-label">Email</label>
                                <p class="form-control">{{ $user->email }}</p>
                            </div> <!-- Phone -->
                            <div class="col-md-6 mb-3"> <label class="form-label">Phone</label>
                                <p class="form-control">{{ $user->phone ?? 'N/A' }}</p>
                            </div> <!-- Address -->
                            <div class="col-md-6 mb-3"> <label class="form-label">Address</label>
                                <p class="form-control">{{ $user->address ?? 'N/A' }}</p>
                            </div> <!-- Role -->
                            <div class="col-md-6 mb-3"> <label class="form-label">Role</label>
                                <p class="form-control">{{ ucfirst($user->role->value) }}</p>
                            </div> <!-- Status -->
                            <div class="col-md-6 mb-3"> <label class="form-label">Status</label>
                                <p class="form-control">{{ ucfirst($user->status->value) }}</p>
                            </div> <!-- Last Login -->
                            <div class="col-md-6 mb-3"> <label class="form-label">Last Login</label>
                                <p class="form-control">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</p>
                            </div> <!-- Departments -->
                            <div class="col-md-6 mb-3"> <label class="form-label">Departments</label>
                                <div class="form-control">
                                    @forelse($user->departments as $department)
                                    <span class="badge bg-info">{{ $department->name }}</span> @empty No
                                        departments assigned
                                    @endforelse
                                </div>
                            </div> <!-- Day Joined -->
                            <div class="col-md-6 mb-3"> <label class="form-label">Day Joined</label>
                                <p class="form-control">
                                    {{ $user->day_joined ? $user->day_joined->format('d M Y') : 'N/A' }} </p>
                            </div> <!-- Birthday -->
                            <div class="col-md-6 mb-3"> <label class="form-label">Birthday</label>
                                <p class="form-control"> {{ $user->birthday ? $user->birthday->format('M d') : 'N/A' }}
                                </p>
                            </div> <!-- What Attracted You -->
                            <div class="col-md-6 mb-3"> <label class="form-label">What Attracted You</label>
                                <p class="form-control">{{ $user->what_attracted_you ?? 'N/A' }}</p>
                            </div> <!-- State of Origin -->
                            <div class="col-md-6 mb-3"> <label class="form-label">State of Origin</label>
                                <p class="form-control">{{ $user->state_of_origin ?? 'N/A' }}</p>
                            </div> <!-- Occupation -->
                            <div class="col-md-6 mb-3"> <label class="form-label">Occupation</label>
                                <p class="form-control">{{ $user->occupation ?? 'N/A' }}</p>
                            </div> <!-- Hobbies -->
                            <div class="col-md-6 mb-3"> <label class="form-label">Hobbies</label>
                                <p class="form-control">{{ $user->hobbies ?? 'N/A' }}</p>
                            </div> <!-- Favourite Quote -->
                            <div class="col-md-6 mb-3"> <label class="form-label">Favourite Quote</label>
                                <p class="form-control">{{ $user->favourite_quote ?? 'N/A' }}</p>
                            </div> <!-- Account Created -->
                            <div class="col-md-6 mb-3"> <label class="form-label">Account Created</label>
                                <p class="form-control">{{ $user->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
