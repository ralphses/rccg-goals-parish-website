@props(['testimony'])

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">View Testimony</h4>
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
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ $testimony->title }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ $testimony->title }}</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            @if ($testimony->media->isNotEmpty())
                                @foreach ($testimony->media as $media)
                                    <div class="mt-2">
                                        @if ($media->media_type === \App\Enums\MediaType::IMAGE)
                                            <img src="{{ Storage::url($media->file_path) }}" alt="{{ $media->title }}" class="img-fluid" style="max-height: 300px;">
                                        @elseif ($media->media_type === \App\Enums\MediaType::VIDEO)
                                            <video controls src="{{ Storage::url($media->file_path) }}" style="max-width: 100%;"></video>
                                        @elseif ($media->media_type === \App\Enums\MediaType::AUDIO)
                                            <audio controls src="{{ Storage::url($media->file_path) }}" class="w-100"></audio>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <p>No media attached to this testimony.</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h3>{{ $testimony->title }}</h3>
                            <p>
                                <strong>Testifier:</strong> {{ $testimony->testifier_name }}
                            </p>
                            <p>
                                <strong>Email:</strong> {{ $testimony->testifier_email ?? 'N/A' }}
                            </p>
                            <p>
                                <strong>Phone:</strong> {{ $testimony->testifier_phone ?? 'N/A' }}
                            </p>
                            <p>
                                <strong>Type:</strong>
                                <span class="badge bg-secondary">{{ $testimony->announcement_type->name }}</span>
                            </p>
                            <p>
                                <strong>Submitted At:</strong>
                                {{ $testimony->created_at->format('d M, Y') }}
                            </p>
                            @if ($testimony->content)
                                <div class="mt-4">
                                    <h5>Content</h5>
                                    <p>{{ $testimony->content }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-action">
                    <a href="{{ route('dashboard.testimonies.edit', $testimony->id) }}" class="btn btn-primary">Edit</a>
                    <a href="{{ route('dashboard.testimonies.index') }}" class="btn btn-info">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</div>