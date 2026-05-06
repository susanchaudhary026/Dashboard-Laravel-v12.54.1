@extends('layouts.app')

@section('content')
<form action="/categories/{{ $category->id }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="title" class="form-label">Category Title</label>
    <input class="form-control" type="text" name="title" value="{{ $category->title }}" placeholder="New category" required>
    </div>
    <div class="form-group mt-2">
      <button type="submit" class="btn btn-primary">Update</button>

    </div>
</form>
@endsection