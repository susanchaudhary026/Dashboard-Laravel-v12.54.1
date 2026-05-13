@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>Live Sessions</h2>
            <hr>

            @if($live && $live->is_live)
                <div class="card border-success mb-4">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Live Now</h4>
                    </div>
                    <div class="card-body">
                        <h5>{{ $live->title }}</h5>
                        <p class="text-muted mb-3">
                            Started by <strong>{{ $live->user->name }}</strong> 
                            • Duration: <strong>{{ $live->getDurationInMinutes() }} minutes</strong>
                        </p>

                        <a href="{{ $live->meeting_link }}" target="_blank" class="btn btn-primary btn-lg mb-4">
                            Join Live Meeting
                        </a>
                    </div>
                </div>

            @else
                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">No Live Session Active</h4>
                    <p>There is no live session currently active.</p>
                    <hr>
                    <p class="mb-0">Please check back later or ask the admin to start a session.</p>
                </div>

                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-camera-video" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="mt-3 text-muted">Waiting for live session to start...</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection