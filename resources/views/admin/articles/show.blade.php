@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Article Details</h2>
        <a href="{{ route('articles.index') }}" style="text-decoration: none; color: #666;">← Back to Table</a>
    </div>
    <hr>

    <div style="margin-bottom: 20px;">
        <h1 style="margin-bottom: 5px;">{{ $article->title }}</h1>
        <p style="color: #666; font-size: 14px;">
            <strong>Category:</strong> {{ $article->category->title ?? 'Uncategorized' }} | 
            <strong>Author:</strong> {{ $article->user->name ?? 'Unknown' }} |
            <strong>Date:</strong> {{ $article->created_at->format('M d, Y') }} |
            <strong>Status:</strong> {{ ucfirst($article->status) }}
        </p>
    </div>

    @if($article->image)
        <div style="width: 100%; background: #f9f9f9; padding: 15px; border-radius: 10px; border: 1px solid #eee; margin-bottom: 25px; text-align: center;">
            <img src="{{ asset('storage/' . $article->image) }}" 
                 style="max-width: 100%; height: auto; border-radius: 5px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        </div>
    @endif

    <div style="line-height: 1.8; font-size: 16px; color: #333;">
        {!! $article->body !!}
    </div>

    <hr style="margin-top: 30px;">
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('articles.edit', $article->id) }}" style="background: blue; color: white; padding: 8px 20px; border-radius: 5px; text-decoration: none;">Edit This Article</a>
        
        <form action="{{ route('articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Delete this article?')">
            @csrf
            @method('DELETE')
            <button type="submit" style="background: red; color: white; padding: 8px 20px; border: none; border-radius: 5px; cursor: pointer;">Delete Article</button>
        </form>
    </div>
</div>
@endsection