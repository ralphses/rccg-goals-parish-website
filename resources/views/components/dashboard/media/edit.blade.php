<x-app-layout title="Edit Media">
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Edit Media</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('dashboard') }}">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.media.index') }}">Media</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="#">Edit</a>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Edit Media</div>
                        </div>
                        <div class="card-body">
                        <form action="{{ route('dashboard.media.update', $media->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            value="{{ $media->title }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category">Category</label>
                                        <select class="form-select" id="category" name="category" required>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->value }}"
                                                    {{ $media->category == $category ? 'selected' : '' }}>
                                                    {{ ucwords(str_replace('_', ' ', $category->value)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="file">Replace Image (optional)</label>
                                        <input type="file" class="form-control" id="file" name="file" onchange="previewImage(event)">
                                    </div>
                                    <img id="image-preview" src="{{ asset('storage/' . $media->file_path) }}" alt="Image preview" class="img-fluid mt-2" style="max-height: 200px;"/>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="is_public" name="is_public"
                                                {{ $media->is_public ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_public">
                                                Public
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-action">
                                <button type="submit" class="btn btn-success">Update Media</button>
                                <a href="{{ route('dashboard.media.index') }}"
                                    class="btn btn-danger">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('image-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endpush

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>