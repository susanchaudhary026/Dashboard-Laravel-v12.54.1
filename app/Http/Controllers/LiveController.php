<?php

namespace App\Http\Controllers;

use App\Models\LiveSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class LiveController extends Controller
{
    public function admin()
    {
    //admin panel
    $live = LiveSession::with('user')->where('is_live', true)->latest()->first();
    return view('live.admin', compact('live'));
    }
    
    //user view
    public function index()
    {
        $live=LiveSession::with('user')->where('is_live',true)->latest()->first();
        return view('live.index',compact('live'));

    }

    //start new live session
    public function start(Request $request)
{
    // Validate input
    $request->validate([
        'title' => 'nullable|string|min:3|max:255|regex:/^[^<>]*$/',
        'meeting_link' => 'required|url'
    ]);

    // Check if user already has active session
    $existing = LiveSession::where('user_id', Auth::id())->where('is_live', true)->first();

    if ($existing) {
        return redirect()->route('live.admin')
            ->with('error', 'You already have an active session. End it first.');
    }

    // End any other active sessions
    LiveSession::where('is_live', true)->where('user_id', '!=', Auth::id())
    ->update([
            'is_live' => false,
            'ended_at' => now()
        ]);

    // Sanitize URL
    $meetingLink = $request->meeting_link;
    if (!str_starts_with($meetingLink, 'https://')) {
        if (str_starts_with($meetingLink, 'http://')) {
            $meetingLink = 'https://' . substr($meetingLink, 7);
        } else {
            $meetingLink = 'https://' . $meetingLink;
        }
    }

    // Create new session
    $session = LiveSession::create([
        'title' => $request->title ? trim($request->title) : 'Live Session',
        'room_name' => 'live-' . Str::random(10),
        'meeting_link' => $meetingLink, 
        'is_live' => true,
        'user_id' => Auth::id(),
        'started_at' => now()
    ]);

    return redirect()->route('live.admin')
        ->with('success', 'Live session started successfully');
}
    
    //end live

    public function end()
    {
        $live = LiveSession::where('is_live', true)->latest()->first();
        if(!$live)
            {
                return redirect()->route('live.admin')->with('error', 'No active live session found.');

            }

            //admin and superadmin who started the live can end
            if(Auth::id() !== $live->user_id && Auth::user()->role !== 'superadmin')
                {
                    return redirect()->route('live.admin')->with('error', 'Unauthorized to end this live session.');
                }
                $live->update(['is_live' => false, 'ended_at' => now()]);
                return redirect()->route('live.admin')->with('success', 'Live session ended!');
    }

    //live hihstory
    public function history()
    {
        $sessions = LiveSession::where('user_id', Auth::id())->orderBy('started_at', 'desc')->paginate(10);
        return view('live.history', compact('sessions'));
    }
}