@props(['testimony', 'announcementTypes'])
@include('components.dashboard.partials.form-shell')

<div class="container"><div class="page-inner"><div class="dashboard-form-shell">
    <div class="dashboard-form-hero card mb-4"><div class="card-body p-4 p-lg-5"><div class="row align-items-center g-4"><div class="col-lg-8"><span class="dashboard-form-eyebrow">Testimonies Form</span><h2 class="dashboard-form-title">Edit this testimony with the current story and media context in view.</h2><p class="dashboard-form-subtitle">Refine contact details, update the content or media, and control featured or approval state from one focused screen.</p></div><div class="col-lg-4"><div class="dashboard-form-hero-actions"><a href="{{ route('dashboard.testimonies.index') }}" class="btn btn-outline-secondary btn-lg dashboard-form-secondary-btn">Back to Testimonies</a><div class="dashboard-form-note"><span class="dot"></span>Existing media remains visible until you replace it</div></div></div></div></div></div>
    <div class="card dashboard-form-card"><div class="card-header"><div class="card-title">Edit Testimony</div></div><div class="card-body">
        <form action="{{ route('dashboard.testimonies.update', $testimony->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label for="testifier_name">Testifier Name</label><input type="text" class="form-control @error('testifier_name') is-invalid @enderror" id="testifier_name" name="testifier_name" value="{{ old('testifier_name', $testimony->testifier_name) }}" required>@error('testifier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                <div class="col-md-6"><div class="form-group"><label for="title">Title</label><input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $testimony->title) }}" required>@error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                <div class="col-md-6"><div class="form-group"><label for="testifier_email">Testifier Email</label><input type="email" class="form-control @error('testifier_email') is-invalid @enderror" id="testifier_email" name="testifier_email" value="{{ old('testifier_email', $testimony->testifier_email) }}">@error('testifier_email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                <div class="col-md-6"><div class="form-group"><label for="testifier_phone">Testifier Phone</label><input type="text" class="form-control @error('testifier_phone') is-invalid @enderror" id="testifier_phone" name="testifier_phone" value="{{ old('testifier_phone', $testimony->testifier_phone) }}">@error('testifier_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                <div class="col-md-6"><div class="form-group"><label for="announcement_type">Announcement Type</label><select class="form-control @error('announcement_type') is-invalid @enderror" id="announcement_type" name="announcement_type">@foreach ($announcementTypes as $type)<option value="{{ $type->value }}" {{ old('announcement_type', $testimony->announcement_type->value) == $type->value ? 'selected' : '' }}>{{ $type->name }}</option>@endforeach</select>@error('announcement_type')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                <div class="col-md-6"><div class="form-group" id="file-group"><label for="file">Media (Image/Video/Audio)</label><input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept="image/*,video/*,audio/*">@error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if ($testimony->media->isNotEmpty())
                        <div class="mt-3"><p class="mb-2">Current media:</p>@foreach ($testimony->media as $media)@if ($media->media_type === \App\enums\MediaType::IMAGE)<img src="{{ $media->file_url }}" alt="{{ $media->title }}" class="img-fluid dashboard-form-preview-image me-2" style="max-height: 100px;">@elseif ($media->media_type === \App\enums\MediaType::VIDEO)<video controls src="{{ $media->file_url }}" class="dashboard-form-preview-image me-2" style="max-width: 200px;"></video>@elseif ($media->media_type === \App\enums\MediaType::AUDIO)<audio controls src="{{ $media->file_url }}"></audio>@endif @endforeach</div>
                    @endif
                </div></div>
                <div class="col-md-12"><div class="form-group" id="content-group"><label for="content">Content</label><textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5">{{ old('content', $testimony->content) }}</textarea>@error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="is_featured" name="is_featured" {{ old('is_featured', $testimony->is_featured) ? 'checked' : '' }}><label class="form-check-label" for="is_featured">Featured</label></div></div>
                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="is_approved" name="is_approved" {{ old('is_approved', $testimony->is_approved) ? 'checked' : '' }}><label class="form-check-label" for="is_approved">Approved</label></div></div>
                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="announce_in_service" name="announce_in_service" {{ old('announce_in_service', $testimony->announce_in_service) ? 'checked' : '' }}><label class="form-check-label" for="announce_in_service">Announce in Service</label></div></div>
            </div>
            <div class="dashboard-form-actions"><button type="submit" class="btn btn-primary dashboard-form-primary-btn">Update Testimony</button><a href="{{ route('dashboard.testimonies.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
        </form>
    </div></div>
</div></div></div>

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
    toggleFields();
    announcementType.addEventListener('change', toggleFields);
});
</script>
