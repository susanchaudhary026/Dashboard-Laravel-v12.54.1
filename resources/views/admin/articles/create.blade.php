@extends('layouts.app')

@section('content')
<div class="card p-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Create New Article</h2>
        <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
    </div>

    <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="mb-3">
            <label class="form-label fw-bold">Article Image</label>
            <div class="input-group">
                <input type="file" name="image" id="imageFile" class="form-control">
                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#mediaModal">
                    <i class="bi bi-images"></i> Browse Media Library
                </button>
            </div>
            <small class="text-muted">Upload a new file or pick one from the library.</small>

            <input type="hidden" name="media_link" id="media_link">
            
            <div id="preview-container" class="mt-2 d-none p-2 border rounded bg-light" style="width: fit-content;">
                <div class="d-flex align-items-center gap-3">
                    <img id="image-preview" src="" style="height: 80px; border: 1px solid #ddd; padding: 2px; border-radius: 4px;">
                    <div>
                        <p class="small mb-1 text-success fw-bold">Library image selected</p>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearMedia()">
                            <i class="bi bi-x-circle"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Title</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Enter article title" required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Category</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="1">Published</option>
                    <option value="0">Draft</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Content</label>
            {{-- CKEditor will replace this textarea --}}
            <textarea name="body" class="form-control" id="editor">{{ old('body') }}</textarea>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary px-5">
                <i class="bi bi-check-circle"></i> Save Article
            </button>
        </div>
    </form>
</div>

<div class="modal fade" id="mediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Media Library</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="p-3 border-bottom bg-white">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" id="mediaSearch" class="form-control" placeholder="Search images by name...">
                </div>
            </div>
            <div class="modal-body bg-light">
                <div class="row g-2" id="media-grid">
                    <p class="text-center py-5">Loading images...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

<script>
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo'],
        })
        .catch(error => { console.error(error); });


    // --- MEDIA LIBRARY LOGIC ---
    let allMediaData = []; 

    document.getElementById('mediaModal').addEventListener('show.bs.modal', function () {
        fetch("{{ route('api.media') }}")
            .then(r => r.json())
            .then(data => {
                allMediaData = data;
                renderMedia(data);
            })
            .catch(err => {
                document.getElementById('media-grid').innerHTML = '<p class="text-center py-5 text-danger">Error: Check if Route [api.media] is defined.</p>';
            });
    });

    document.getElementById('mediaSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const filtered = allMediaData.filter(f => f.name.toLowerCase().includes(term));
        renderMedia(filtered);
    });

    function renderMedia(data) {
        const grid = document.getElementById('media-grid');
        if(data.length === 0) {
            grid.innerHTML = '<div class="col-12 text-center py-5 text-muted">No images found.</div>';
            return;
        }
        grid.innerHTML = data.map(f => `
            <div class="col-md-3 col-6">
                <div class="card h-100 shadow-sm border-0">
                    <img src="${f.url}" class="card-img-top selectable-img" 
                         style="cursor:pointer; height:120px; object-fit:cover;" 
                         onclick="setMedia('${f.path}', '${f.url}')">
                    <div class="card-footer p-1 bg-white border-0 text-center">
                        <small class="text-truncate d-block" style="font-size: 11px;">${f.name}</small>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function setMedia(path, url) {
        document.getElementById('media_link').value = path;
        document.getElementById('image-preview').src = url;
        document.getElementById('preview-container').classList.remove('d-none');
        document.getElementById('imageFile').value = ''; 
        bootstrap.Modal.getInstance(document.getElementById('mediaModal')).hide();
    }

    function clearMedia() {
        document.getElementById('media_link').value = '';
        document.getElementById('image-preview').src = '';
        document.getElementById('preview-container').classList.add('d-none');
    }

    document.getElementById('imageFile').addEventListener('change', function() {
        if (this.value) clearMedia();
    });
</script>

<style>
    .ck-editor__editable_inline {
        min-height: 300px;
    }
    .selectable-img:hover {
        transform: scale(1.03);
        outline: 3px solid #0d6efd;
        transition: 0.2s;
    }
</style>
@endsection