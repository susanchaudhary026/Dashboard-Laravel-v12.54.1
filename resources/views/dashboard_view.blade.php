@extends('layouts.app')

@section('content')
<div class="card mb-4 p-3">
    <h2>Dashboard</h2>
    <a href="{{ route('articles.create') }}">
        <button class="btn btn-primary btn-sm">Add Article</button>
    </a>
    <hr>
    
    <strong>Categories:</strong>
    <div class="category-list mt-2">
        @foreach($categories as $category)
            <a href="{{ route('categories.show', $category->id) }}" class="badge bg-secondary text-decoration-none mr-1">
                {{ $category->title }}
            </a>
        @endforeach
    </div>
</div>

@foreach($articles as $article)
    <div class="card mb-4 p-3">
        @if($article->image)
            <div style="width: 100%; background: #f9f9f9; border-radius: 8px; margin-bottom: 15px; text-align: center; border: 1px solid #eee;">
                <img src="{{ asset('storage/' . $article->image) }}"
                     alt="{{ $article->title }}" 
                     style="max-width: 100%; height: auto; max-height: 450px; display: block; margin: 0 auto; border-radius: 8px;">
            </div>
        @endif

        <h3>{{ $article->title }}</h3>
        <p>
            <strong>Status:</strong> 
            @php
                $is_published = (int)$article->status === 1;
                $status_label = $is_published ? 'Published' : 'Unpublished';
                $status_color = $is_published ? '#28a745' : '#dc3545';
            @endphp
            <span style="font-weight: bold";>
                {{ $status_label }}
            </span>
        </p>
        <p>
            <strong>Published on:</strong> 
            {{ $article->created_at->format('M d, Y') }}
        </p>
        <p>
            <strong>Author:</strong> 
            {{ $article->user->name ?? 'Unknown Author' }}
        </p>

        <p>
            <strong>Category:</strong> 
            {{ $article->category->title ?? 'Uncategorized' }}
        </p>

        <p>{!! $article->body !!}</p>

        <div style="display: flex; gap: 10px; margin-top: 15px; align-items: center;">
            <a href="{{ route('articles.show', $article->id) }}" 
               style="box-shadow:inset 0px 1px 0px 0px #ffffff; background:linear-gradient(to bottom, #ededed 5%, #dfdfdf 100%); background-color:#ededed; border-radius:6px; border:1px solid #dcdcdc; display:inline-block; cursor:pointer; color:#777777; font-family:Arial; font-size:15px; font-weight:bold; padding:6px 24px; text-decoration:none; text-shadow:0px 1px 0px #ffffff;">
                View Details
            </a>

            @if(Auth::user()->role == 'admin' || Auth::id() === $article->user_id)
                <a href="{{ route('articles.edit', $article->id) }}" 
                   style="background: blue; color: white; border: none; padding: 8px 20px; border-radius: 5px; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; height: 35px;">
                   Edit
                </a>

                <form action="{{ route('articles.destroy', $article->id) }}" method="POST" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Delete article?')" 
                            style="background: red; color: white; border: none; padding: 8px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; height: 35px;">
                        Delete
                    </button>
                </form>
            @endif
        </div>
    </div>
@endforeach

<div style="margin-top: 30px; margin-bottom: 30px; display: flex; justify-content: center;">
    {{ $articles->links('pagination::bootstrap-4') }}
</div>
@endsection