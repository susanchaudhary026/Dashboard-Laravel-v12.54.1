<?php

namespace App\Http\Controllers;

use App\Models\LiveSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LiveController extends Controller
{
    public function admin()
    {
        $live = LiveSession::where('is_live', true)->latest()->first();
        return view('live.admin', compact('live'));
    }

    public function index()
    {
        $live = LiveSession::where('is_live', true)->latest()->first();
        return view('live.index', compact('live'));
    }

    public function start(Request $request)
    {
        LiveSession::where('is_live', true)->update([
            'is_live' => false,
            'ended_at' => now()
        ]);

        LiveSession::create([
            'title' => $request->title ?? 'Live Session',
            'room_name' => 'live-' . Str::random(10),
            'is_live' => true,
            'user_id' => auth()->user()->id,
            'started_at' => now()
        ]);

        return redirect()->route('live.admin');
    }

    public function end()
    {
        LiveSession::where('is_live', true)->update([
            'is_live' => false,
            'ended_at' => now()
        ]);

        return redirect()->route('live.admin');
    }
}