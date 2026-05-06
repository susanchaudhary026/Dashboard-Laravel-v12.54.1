@extends('layouts.app')
@section('content')
<form action="{{ route('password.email') }}" method="POST" class="p-4 border rounded shadow-sm mx-auto" style="max-width: 400px;">
    @csrf
    <h4>Forgot Password</h4>
    @if (session('status')) <div class="alert alert-info small">{{ session('status') }}</div> @endif
    <input type="email" name="email" class="form-control mb-3" placeholder="Enter Email" required>
    <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
</form>


@endsection