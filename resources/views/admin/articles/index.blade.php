@extends('layouts.app')

@section('content')
<div class="card">
    <div style="margin-bottom: 20px; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
        <form method="GET" action="{{ route('articles.index') }}" id="filterForm" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
            
            <div style="flex: 1.5; min-width: 200px;">
                <label style="display:block; font-size: 11px; font-weight: bold; color: #666; margin-bottom: 5px;">SEARCH</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Keyword..." class="form-control">
            </div>

            <div style="flex: 1; min-width: 150px;">
                <label style="display:block; font-size: 11px; font-weight: bold; color: #666; margin-bottom: 5px;">CATEGORY</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 130px;">
                <label style="display:block; font-size: 11px; font-weight: bold; color: #666; margin-bottom: 5px;">FROM</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
            </div>

            <div style="flex: 1; min-width: 130px;">
                <label style="display:block; font-size: 11px; font-weight: bold; color: #666; margin-bottom: 5px;">TO</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary btn-sm px-3" style="font-weight: bold;">Filter</button>
                <a href="{{ route('articles.export', request()->all()) }}" class="btn btn-success btn-sm px-3" style="font-weight: bold;">Export</a>
                <a href="{{ route('articles.index') }}" class="btn btn-secondary btn-sm px-3">Reset</a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" style="background: #fff; border: 1px solid #ddd; border-radius: 8px;">
            <thead class="table-light">
                <tr>
                    <th style="padding: 12px;">Photo</th>
                    <th style="padding: 12px;">Title</th>
                    <th style="padding: 12px;">Category</th>
                    <th style="padding: 12px;">Status</th>
                    <th style="padding: 12px;">
                        <a href="{{ route('articles.index', array_merge(request()->all(), ['sort' => $sortOrder == 'asc' ? 'desc' : 'asc'])) }}" 
                           style="text-decoration: none; color: #333; font-weight: bold; display: flex; align-items: center; gap: 5px;">
                            Created Date 
                            <span>{{ $sortOrder == 'asc' ? '↑' : '↓' }}</span>
                        </a>
                    </th>
                    <th style="padding: 12px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articles as $article)
                    <tr>
                        <td style="padding: 10px;">
                            @if($article->image)
                                <img src="{{ $article->image }}" alt="{{ $article->title }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">No Image</span>
                                </div>
                            @endif
                        </td>
                        <td style="padding: 10px; font-weight: 500;">{{ Str::limit($article->title, 40) }}</td>
                        <td style="padding: 10px;">
                            <span class="badge text-bg-light border">{{ $article->category->title ?? 'N/A' }}</span>
                        </td>
                        <td style="padding: 10px;">
                            @php
                                $is_published = (int)$article->status === 1;
                                $color = $is_published ? '#28a745' : '#dc3545';
                                $canManage = (Auth::user()->role !== 'user' ||  Auth::id() == $article->user_id);
                            @endphp

                            @if($canManage)
                                <button type="button" data-bs-toggle="modal" data-bs-target="#statusModal{{ $article->id }}"
                                {{ ucfirst($article->status) }}
                                     style="background: {{ $color }}; color: white; border: none; padding: 6px 15px; border-radius: 20px; cursor: pointer; font-size: 11px; min-width: 90px;">
                                    {{ $is_published ? 'Published' : 'Unpublished' }}
                                </button>
                            @else
                                <span 
                                {{ ucfirst($article->status) }}
                                style="background: {{ $color }}; color: white; padding: 6px 15px; border-radius: 20px; font-size: 11px; min-width: 90px; display: inline-block; text-align: center;">
                                    {{ $is_published ? 'Published' : 'Unpublished' }}
                                </span>
                            @endif
                        </td>
                        <td style="padding: 10px; font-size: 13px; color: #666;">
                            {{ $article->created_at->format('Y-m-d') }}
                        </td>
                        <td style="padding: 10px;">
                            <div class="d-flex gap-2">
                                <a href="{{ route('articles.show', $article->id) }}" class="btn btn-sm btn-outline-success">View</a>
                                
                                @if($canManage)
                                    <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-sm btn-primary" style="padding: 4px 10px; font-size: 12px; text-decoration: none; border-radius: 4px;">Edit</a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $article->id }}">Delete</button>
                                @endif
                            </div>

                            @if($canManage)
                            <div class="modal fade" id="statusModal{{ $article->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-sm modal-dialog-centered">
                                    <div class="modal-content text-center">
                                        <div class="modal-body py-4">
                                            <p>Change status to <strong>{{ $is_published ? 'Unpublished' : 'Published' }}</strong>?</p>
                                            <form action="{{ route('articles.toggleStatus', $article->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="d-flex justify-content-center gap-2 mt-3">
                                                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-sm btn-primary">Confirm</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="deleteModal{{ $article->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-danger">Delete Article</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            Are you sure you want to delete <strong>{{ $article->title }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <form action="{{ route('articles.destroy', $article->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $articles->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection