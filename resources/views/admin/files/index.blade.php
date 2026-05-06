@extends('layouts.app')

@section('content')
<div class="card p-4 shadow-sm" style="overflow: visible;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-folder2-open text-primary"></i> File Manager</h2>
        <span class="text-muted small">Path: <strong>{{ $currentPath }}</strong></span>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                @php $link = ''; @endphp
                @foreach($breadcrumbs as $crumb)
                    @php $link .= ($loop->first ? '' : '/') . $crumb; @endphp
                    <li class="breadcrumb-item">
                        <a href="{{ route('files.index', ['path' => $link]) }}" class="text-decoration-none text-primary fw-bold">
                            {{ ucfirst($crumb) }}
                        </a>
                    </li>
                @endforeach
            </ol>
        </nav>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#folderModal">
            <i class="bi bi-folder-plus"></i> New Folder
        </button>
    </div>

    <div id="file-dropzone" class="dropzone mb-4" style="border: 2px dashed #0d6efd; border-radius: 12px; background: #f8f9fa;">
        <div class="dz-message py-3">
            <i class="bi bi-cloud-arrow-up" style="font-size: 2rem; color: #0d6efd;"></i>
            <h6 class="mt-2">Drag & Drop files here to upload to this folder</h6>
        </div>
    </div>

    <hr>

    <div class="row mt-4" style="overflow: visible;">
        @forelse($allItems as $item)
            <div class="col-xl-2 col-lg-3 col-md-4 col-6 mb-4 card-container">
                <div class="card h-100 border-0 shadow-sm text-center p-2 file-item">
                    
                    @if($item['type'] == 'folder')
                        <a href="{{ route('files.index', ['path' => $item['path']]) }}" class="text-decoration-none">
                            <div class="file-preview mb-2" style="height: 120px; background: #fff9e6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-folder-fill text-warning" style="font-size: 4rem;"></i>
                            </div>
                            <div class="small fw-bold text-dark text-truncate px-1">{{ $item['name'] }}</div>
                        </a>
                        <div class="mt-2 small text-muted">Folder</div>
                    @else
                        <div class="file-preview mb-2" style="height: 120px; background: #f1f1f1; border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            @php $ext = pathinfo($item['name'], PATHINFO_EXTENSION); @endphp
                            @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif','webp']))
                                <img src="{{ $item['url'] }}" class="img-fluid" style="object-fit: cover; height: 100%; width: 100%;">
                            @else
                                <i class="bi bi-file-earmark-text text-secondary" style="font-size: 3rem;"></i>
                            @endif
                        </div>

                        <div class="small fw-bold text-truncate px-1" title="{{ $item['name'] }}">{{ $item['name'] }}</div>
                        
                        <div class="dropdown mt-2" style="position: static;"> {{-- Added static position to dropdown container --}}
                            <button class="btn btn-sm btn-light border w-100 dropdown-toggle" 
                                    type="button" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-boundary="viewport"
                                    data-bs-display="static"> 
                                Actions
                            </button>
                            <ul class="dropdown-menu shadow w-100">
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="copyLink('{{ $item['url'] }}')"><i class="bi bi-link-45deg text-info"></i> Copy Link</a></li>
                                <li><a class="dropdown-item" href="{{ $item['url'] }}" download><i class="bi bi-download text-success"></i> Download</a></li>
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="openTransferModal('move', '{{ addslashes($item['path']) }}', '{{ addslashes($item['name']) }}')"><i class="bi bi-arrows-move text-primary"></i> Move File</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="openTransferModal('copy', '{{ addslashes($item['path']) }}', '{{ addslashes($item['name']) }}')"><i class="bi bi-files text-secondary"></i> Copy File</a></li>
                                
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('files.destroy') }}" method="POST" onsubmit="return confirm('Delete permanently?')">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="path" value="{{ $item['path'] }}">
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash"></i> Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted">This folder is empty.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- MODALS --}}
<div class="modal fade" id="folderModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('files.createFolder') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="current_path" value="{{ $currentPath }}">
            <div class="modal-header">
                <h5 class="modal-title">New Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-bold">Folder Name</label>
                <input type="text" name="folder_name" class="form-control" placeholder="Enter folder name" required autofocus>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Folder</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="transferForm" method="POST" class="modal-content shadow-lg border-0">
            @csrf
            <input type="hidden" name="file_path" id="transfer_file_path">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="transferTitle">Transfer File</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2 text-muted">Item: <strong id="transfer_file_name" class="text-dark"></strong></p>
                <label class="form-label fw-bold">Select Destination Folder:</label>
                <select name="destination" class="form-select border-primary" required>
                    <option value="uploads">/uploads (Main)</option>
                    <option value="articles">/articles</option>
                    @foreach($allFolders as $folder)
                        <option value="{{ $folder }}">{{ $folder }}</option>
                    @endforeach
                </select>
                <p class="mt-2 small text-muted">Choosing a folder acts as the "Paste" action.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="transferBtn">Apply</button>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

<script>
    Dropzone.autoDiscover = false;
    new Dropzone("#file-dropzone", {
        url: "{{ route('files.upload') }}",
        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
        params: { 'path': "{{ $currentPath }}" },
        success: function() { location.reload(); },
        error: function(file, response) { alert("Upload failed"); }
    });

    function copyLink(url) {
        navigator.clipboard.writeText(url).then(() => {
            alert("URL Copied to clipboard!");
        });
    }

    function openTransferModal(type, path, name) {
        const form = document.getElementById('transferForm');
        const title = document.getElementById('transferTitle');
        const btn = document.getElementById('transferBtn');
        
        document.getElementById('transfer_file_path').value = path;
        document.getElementById('transfer_file_name').innerText = name;
        
        if(type === 'move') {
            form.action = "{{ route('files.move') }}";
            title.innerHTML = '<i class="bi bi-arrows-move"></i> Move File';
            btn.innerText = "Move to Folder";
            btn.className = "btn btn-primary";
        } else {
            form.action = "{{ route('files.copy') }}";
            title.innerHTML = '<i class="bi bi-files"></i> Copy File';
            btn.innerText = "Copy to Folder";
            btn.className = "btn btn-success";
        }
        
        new bootstrap.Modal(document.getElementById('transferModal')).show();
    }
</script>

<style>
    .card-container {
        position: relative;
        z-index: 1;
    }

    .card-container:hover {
        z-index: 1000;
    }

    .file-item { 
        transition: transform 0.2s ease-in-out; 
        position: relative;
        overflow: visible !important; {{-- Crucial for dropdown --}}
    }

    .file-item:hover { 
        transform: translateY(-5px); 
        border: 1px solid #0d6efd !important; 
    }

    .dropdown-menu { 
        margin-top: 0 !important; 
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        z-index: 1050;
    }

    .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; }
</style>
@endsection