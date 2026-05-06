@extends('layouts.app')

@section('content')
<div class="card">
    <div class="d-flex justify-content-between align-items-center mb-4 p-3">
        <h4 class="m-0">Categories Management</h4>
        {{-- Only Admin/Superadmin can see Add Button --}}
        @if(Auth::user()->role !== 'user')
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                Add New Category
            </button>
        @endif
    </div>

    {{-- Create Modal - Only for Admin/Superadmin --}}
    @if(Auth::user()->role !== 'user')
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 text-start">
                            <label class="form-label fw-bold small">CATEGORY TITLE</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter category name" required>
                        </div>
                        <div class="mb-3 text-start">
                            <label class="form-label fw-bold small">STATUS</label>
                            <select name="status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle" style="background: #fff; border: 1px solid #ddd; border-radius: 8px;">
            <thead class="table-light">
                <tr>
                    <th style="padding: 12px; width: 60%;">Title</th>
                    <th style="padding: 12px;">Status</th>
                    @if(Auth::user()->role !== 'user')
                        <th style="padding: 12px;">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td style="padding: 12px; font-weight: 500;">
                            {{ $category->title }}
                        </td>
                        <td style="padding: 10px;">
                            @php
                                $is_published = (int)$category->status === 1;
                                $color = $is_published ? '#28a745' : '#dc3545';
                            @endphp

                            @if(Auth::user()->role === 'user')
                                <span 
                                 {{ ucfirst($category->status) }}
                                style="background: {{ $color }}; color: white; padding: 6px 15px; border-radius: 20px; font-size: 11px; min-width: 90px; display: inline-block; text-align: center;">
                                    {{ $is_published ? 'Published' : 'Unpublished' }}
                                </span>
                            @else
                                <button type="button" data-bs-toggle="modal" data-bs-target="#statusCat{{ $category->id }}"
                                {{ ucfirst($category->status) }} 
                                  style="background: {{ $color }}; color: white; border: none; padding: 6px 15px; border-radius: 20px; cursor: pointer; font-size: 11px; min-width: 90px;">
                                    {{ $is_published ? 'Published' : 'Unpublished' }}
                                </button>
                            @endif
                        </td>
                        
                        @if(Auth::user()->role !== 'user')
                        <td style="padding: 12px;">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editCat{{ $category->id }}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#delCat{{ $category->id }}">
                                    Delete
                                </button>
                            </div>

                            <div class="modal fade" id="statusCat{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-sm modal-dialog-centered">
                                    <div class="modal-content text-center">
                                        <div class="modal-body py-4">
                                            <p>Change <strong>{{ $category->title }}</strong> to <strong>{{ $is_published ? 'Unpublished' : 'Published' }}</strong>?</p>
                                            <form action="{{ route('categories.toggleStatus', $category->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <div class="d-flex justify-content-center gap-2 mt-3">
                                                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">No</button>
                                                    <button type="submit" class="btn btn-sm btn-primary">Yes, Change</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editCat{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('categories.update', $category->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Category</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small">CATEGORY TITLE</label>
                                                    <input type="text" name="title" class="form-control" value="{{ $category->title }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small">STATUS</label>
                                                    <select name="status" class="form-select">
                                                        <option value="1" {{ $category->status == 1 ? 'selected' : '' }}>Published</option>
                                                        <option value="0" {{ $category->status == 0 ? 'selected' : '' }}>Unpublished</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Category</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Delete Modal --}}
                            <div class="modal fade" id="delCat{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-danger">Delete Category</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            Are you sure you want to delete <strong>"{{ $category->title }}"</strong>? 
                                            <br><small class="text-muted">Warning: This may affect articles in this category.</small>
                                        </div>
                                        <div class="modal-footer">
                                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Confirm Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection