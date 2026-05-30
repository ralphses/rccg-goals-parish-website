@props(['media'])

<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">View Media</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                @if ($media->media_type === \App\Enums\MediaType::IMAGE)
                                    <img src="{{ asset('storage/' . $media->file_path) }}" class="img-fluid" alt="{{ $media->title }}">
                                @elseif ($media->media_type === \App\Enums\MediaType::VIDEO)
                                    <video controls class="img-fluid">
                                        <source src="{{ asset('storage/' . $media->file_path) }}" type="{{ Storage::disk('public')->mimeType($media->file_path) }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @elseif ($media->media_type === \App\Enums\MediaType::AUDIO)
                                    <audio controls class="w-100">
                                        <source src="{{ asset('storage/' . $media->file_path) }}" type="{{ Storage::disk('public')->mimeType($media->file_path) }}">
                                        Your browser does not support the audio element.
                                    </audio>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h3>{{ $media->title }}</h3>
                                <p>
                                    <strong>Type:</strong>
                                    <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $media->media_type->value)) }}</span>
                                </p>
                                <p>
                                    <strong>Category:</strong>
                                    <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $media->category->value)) }}</span>
                                </p>
                                <p>
                                    <strong>Uploaded By:</strong>
                                    @if ($media->mediable_type === 'App\\Models\\User')
                                        {{ $media->mediable->name ?? 'N/A' }}
                                    @elseif ($media->mediable_type === 'App\\Models\\Testimony')
                                        {{ $media->mediable->testifier_name ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                                <p>
                                    <strong>Uploaded At:</strong>
                                    {{ $media->created_at->format('d M, Y') }}
                                </p>
                                <p>
                                    <strong>File Size:</strong>
                                    {{ number_format($media->size / 1024, 2) }} KB
                                </p>
                                <p>
                                    <strong>Public:</strong>
                                    {{ $media->is_public ? 'Yes' : 'No' }}
                                </p>
                                <a href="{{ route('dashboard.media.edit', $media->id) }}" class="btn btn-primary">Edit</a>
                                <a href="{{ route('dashboard.media.index') }}" class="btn btn-secondary">Back to Media</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>