@extends('layouts.app')

@section('content')
<form action="/categories" method="POST">
    @csrf
    <input type="text" name="title" placeholder="New category" required>
    <button type="submit">Add Category</button>
</form>
@if ($errors->any())
    <div style="color: darkred;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@endsection