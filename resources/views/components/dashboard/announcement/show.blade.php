@props(['announcement'])

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">View Announcement</h4>
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
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ $announcement->title }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ $announcement->title }}</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h3>{{ $announcement->title }}</h3>
                            <p>
                                <strong>Creator:</strong> {{ $announcement->creator->name }}
                            </p>
                            <p>
                                <strong>Service Date:</strong> {{ $announcement->service_date->format('d M, Y') }}
                            </p>
                            <p>
                                <strong>Frequency:</strong> {{ $announcement->frequency->name }}
                            </p>
                            <p>
                                <strong>Status:</strong>
                                @if ($announcement->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </p>
                            <p>
                                <strong>Approval:</strong>
                                @if ($announcement->is_approved)
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Not Approved</span>
                                @endif
                            </p>
                            <div class="mt-4">
                                <h5>Content</h5>
                                <p>{{ $announcement->content }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            @if ($announcement->media->isNotEmpty())
                                <h5>Media</h5>
                                @foreach ($announcement->media as $media)
                                    <div class="mt-2">
                                        @if ($media->media_type === \App\Enums\MediaType::IMAGE)
                                            <img src="{{ Storage::url($media->file_path) }}" alt="{{ $media->title }}" class="img-fluid" style="max-height: 300px;">
                                        @elseif ($media->media_type === \App\Enums\MediaType::VIDEO)
                                            <video controls src="{{ Storage::url($media->file_path) }}" style="max-width: 100%;"></video>
                                        @endif
                                        <a href="{{ Storage::url($media->file_path) }}" class="btn btn-sm btn-secondary mt-2" download>Download</a>
                                    </div>
                                @endforeach
                            @else
                                <p>No media attached to this announcement.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-action">
                    @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
                        @if (!$announcement->is_approved)
                            <form action="{{ route('dashboard.announcements.approve', $announcement->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">Approve</button>
                            </form>
                        @endif
                    @endif
                    <a href="{{ route('dashboard.announcements.edit', $announcement->id) }}" class="btn btn-primary">Edit</a>
                    <a href="{{ route('dashboard.announcements.index') }}" class="btn btn-info">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</div>