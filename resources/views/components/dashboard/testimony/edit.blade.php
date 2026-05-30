@props(['testimony', 'announcementTypes'])

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Edit Testimony</h4>
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
                <a href="#">Edit: {{ $testimony->title }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Edit Testimony</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.testimonies.update', $testimony->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="testifier_name">Testifier Name</label>
                                    <input type="text" class="form-control @error('testifier_name') is-invalid @enderror" id="testifier_name" name="testifier_name" value="{{ old('testifier_name', $testimony->testifier_name) }}" required>
                                    @error('testifier_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $testimony->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="testifier_email">Testifier Email</label>
                                    <input type="email" class="form-control @error('testifier_email') is-invalid @enderror" id="testifier_email" name="testifier_email" value="{{ old('testifier_email', $testimony->testifier_email) }}">
                                    @error('testifier_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="testifier_phone">Testifier Phone</label>
                                    <input type="text" class="form-control @error('testifier_phone') is-invalid @enderror" id="testifier_phone" name="testifier_phone" value="{{ old('testifier_phone', $testimony->testifier_phone) }}">
                                    @error('testifier_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="announcement_type">Announcement Type</label>
                                    <select class="form-control @error('announcement_type') is-invalid @enderror" id="announcement_type" name="announcement_type">
                                        @foreach ($announcementTypes as $type)
                                            <option value="{{ $type->value }}" {{ old('announcement_type', $testimony->announcement_type->value) == $type->value ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('announcement_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="file-group">
                                    <label for="file">Media (Image/Video/Audio)</label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept="image/*,video/*,audio/*">
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if ($testimony->media->isNotEmpty())
                                        <div class="mt-2">
                                            <p>Current media:</p>
                                            @foreach ($testimony->media as $media)
                                                @if ($media->media_type === \App\Enums\MediaType::IMAGE)
                                                    <img src="{{ Storage::url($media->file_path) }}" alt="{{ $media->title }}" class="img-fluid" style="max-height: 100px;">
                                                @elseif ($media->media_type === \App\Enums\MediaType::VIDEO)
                                                    <video controls src="{{ Storage::url($media->file_path) }}" style="max-width: 200px;"></video>
                                                @elseif ($media->media_type === \App\Enums\MediaType::AUDIO)
                                                    <audio controls src="{{ Storage::url($media->file_path) }}"></audio>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="content-group">
                            <label for="content">Content</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5">{{ old('content', $testimony->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_featured" name="is_featured" {{ old('is_featured', $testimony->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Featured
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_approved" name="is_approved" {{ old('is_approved', $testimony->is_approved) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_approved">
                                        Approved
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="announce_in_service" name="announce_in_service" {{ old('announce_in_service', $testimony->announce_in_service) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="announce_in_service">
                                        Announce in Service
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-action">
                            <button type="submit" class="btn btn-success">Update Testimony</button>
                            <a href="{{ route('dashboard.testimonies.index') }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const announcementType = document.getElementById('announcement_type');
        const contentGroup = document.getElementById('content-group');
        const contentTextarea = document.getElementById('content');
        const fileInput = document.getElementById('file');

        function toggleFields() {
            const selectedType = announcementType.value;

            if (selectedType === 'video' || selectedType === 'audio') {
                contentGroup.style.display = 'none';
                contentTextarea.removeAttribute('required');
                fileInput.removeAttribute('required');
            } else {
                contentGroup.style.display = 'block';
                contentTextarea.setAttribute('required', 'required');
                fileInput.removeAttribute('required');
            }
        }

        // Initial check
        toggleFields();

        // Event listener
        announcementType.addEventListener('change', toggleFields);
    });
</script>