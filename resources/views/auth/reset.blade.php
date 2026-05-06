@extends('layouts.app')
@section('content')
<form action="{{ route('password.update') }}" method="POST" class="p-4 border rounded shadow-sm mx-auto" style="max-width: 400px;">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <h4>Set New Password</h4>
    <input type="email" name="email" class="form-control mb-2" placeholder="Confirm Email" required>
    <input type="password" name="password" class="form-control mb-2" placeholder="New Password" required>
    <input type="password" name="password_confirmation" class="form-control mb-3" placeholder="Confirm Password" required>
    <button type="submit" class="btn btn-success w-100">Update Password</button>
</form>
@endsection