@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Edit Article</h2>
    <form action="{{ route('articles.update', $article->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <label>Title</label>
        <input type="text" name="title" placeholder="Article title" value="{{ old('title', $article->title) }}" required>

        <label>Category</label>
        <select name="category_id" required>
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->title }}
                </option>
            @endforeach
        </select>

        <label>Status</label>
        <select name="status" required>
            <option value="0" {{ old('status', $article->status) == 0 ? 'selected' : '' }}>Unpublished</option>
            <option value="1" {{ old('status', $article->status) == 1 ? 'selected' : '' }}>Published</option>
        </select>

        <label>Article Image</label>
        @if($article->image)
            <div style="margin-bottom: 10px;">
                <img src="{{ asset('storage/' . $article->image) }}" width="150" style="border-radius: 5px;">
                <p style="font-size: 12px; color: gray;">Current Image</p>
            </div>
        @endif
        <input type="file" name="image" accept="image/*">

        <label>Content</label>
        <textarea name="body" id="editor">{{ old('body', $article->body) }}</textarea>
 
        <button type="submit" style="margin-top: 20px;">Update Article</button>
    </form>

    <br>
    <a href="{{ route('articles.index') }}">Back</a>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => {
            console.error(error);
        });
</script>
@endsection