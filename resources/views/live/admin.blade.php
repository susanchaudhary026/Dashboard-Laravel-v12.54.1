@extends('layouts.app')

@section('content')
@if($errors->any())
    <div class="alert alert-danger">
        <strong>Errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>Live Session Admin Panel</h2>
            <hr>

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Errors:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($live && $live->is_live)
                <div class="card border-success mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Active Live Session</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Title:</strong> {{ $live->title }}</p>
                                <p><strong>Started by:</strong> {{ $live->user->name }}</p>
                                <p><strong>Started At:</strong> {{ $live->started_at->format('M d, Y H:i A') }}</p>
                                <p><strong>Duration:</strong> <span class="badge bg-info">{{ $live->getDurationInMinutes() }} minutes</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Meeting Link:</strong></p>
                                <a href="{{ $live->meeting_link }}" target="_blank" class="btn btn-primary">
                                    Open Meeting Link
                                </a>
                            </div>
                        </div>

                        <hr>

                        <div>
                            <form action="{{ route('live.end') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to end this session?')">
                                    End Session
                                </button>
                            </form>

                            <a href="{{ route('live.history') }}" class="btn btn-secondary">View History</a>
                        </div>
                    </div>
                </div>
                    
            @else
                 <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">No Active Live Session</h4>
                    <p>There is currently no live session active.</p>
                    <hr>
                    <p class="mb-0">Use the form below to start a new live session.</p>
                </div>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Start a New Live Session</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('live.start') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Session Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" 
                                       placeholder="e.g., Product Launch Meeting, Team Standup"
                                       value="{{ old('title') }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Optional - will default to "Live Session" if empty</small>
                            </div>

                            <div class="mb-3">
                                <label for="meeting_link" class="form-label">Google Meet Link <span class="text-danger">*</span></label>
                                <input type="url" class="form-control @error('meeting_link') is-invalid @enderror" 
                                       id="meeting_link" name="meeting_link" 
                                       placeholder="https://meet.google.com/abc-defg-hij"
                                       value="{{ old('meeting_link') }}" required>
                                @error('meeting_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 d-sm-flex">
                                <button type="submit" class="btn btn-success btn-lg">
                                    Start Live Session
                                </button>
                                <a href="{{ route('live.history') }}" class="btn btn-outline-secondary btn-lg">
                                    View History
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection