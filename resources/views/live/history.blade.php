@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>Live Session History</h2>
            <hr>

            @if($sessions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Session Title</th>
                                <th>Started At</th>
                                <th>Ended At</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                            <tr>
                                <td>
                                    <strong>{{ $session->title }}</strong>
                                </td>
                                <td>
                                    {{ $session->started_at->format('M d, Y H:i A') }}
                                </td>
                                <td>
                                    @if($session->ended_at)
                                        {{ $session->ended_at->format('M d, Y H:i A') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $session->getDurationInMinutes() }} min</span>
                                </td>
                                <td>
                                    @if($session->is_live)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Ended</span>
                                    @endif
                                </td>
                                <td>
                                    @if($session->meeting_link)
                                        <a href="{{ $session->meeting_link }}" target="_blank" class="btn btn-sm btn-primary">
                                            Open Link
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $sessions->links() }}
                </div>
            @else
                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">No Sessions Yet</h4>
                    <p>You haven't started any live sessions yet.</p>
                    <hr>
                    <p class="mb-0">
                        <a href="{{ route('live.admin') }}" class="alert-link">Start a live session now</a>
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection