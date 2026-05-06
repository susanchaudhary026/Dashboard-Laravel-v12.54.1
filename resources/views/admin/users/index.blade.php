@extends('layouts.app')

@section('content')
<div class="card p-4">
    <h2 class="mb-4">User Management</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover border">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Current Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge bg-{{ $user->role == 'superadmin' ? 'danger' : ($user->role == 'admin' ? 'warning' : 'info') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('users.updateRole', $user->id) }}" method="POST" class="d-flex">
                            @csrf
                            <select name="role" class="form-select form-select-sm me-2" style="width: 150px;" {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary" {{ $user->id === Auth::id() ? 'disabled' : '' }}>Update</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection