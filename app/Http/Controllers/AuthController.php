<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user'
        ]);

        return redirect('/')->with('success', 'Account created successfully');
    }
    public function showForgot() {
    return view('auth.forgot');
}

public function sendResetLink(Request $request) 
{
    $request->validate(['email' => 'required|email|exists:users,email']);

    // Clean up old tokens
    \DB::table('password_resets')->where('email', $request->email)->delete();

    $token = \Illuminate\Support\Str::random(64);

    \DB::table('password_resets')->insert([
        'email' => $request->email,
        'token' => $token,
        'created_at' => now()
    ]);

    $resetLink = route('password.reset', ['token' => $token]);

    
    Mail::raw("To reset your password, please click the following link: {$resetLink}", function ($message) use ($request) {
    $message->from(config('mail.from.address'), config('mail.from.name'))
            ->to($request->email)
            ->subject('Password Reset Request');
    });

    return back()->with('status', 'A reset link has been sent to your email.');
}

public function showReset($token) {
    return view('auth.reset', ['token' => $token]);
}

public function updatePassword(Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email|exists:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    $reset = DB::table('password_resets')
        ->where(['email' => $request->email, 'token' => $request->token])
        ->first();

    if (!$reset) {
        return back()->withErrors(['email' => 'Invalid token or email.']);
    }

    User::where('email', $request->email)->update([
        'password' => Hash::make($request->password)
    ]);

    DB::table('password_resets')->where(['email' => $request->email])->delete();

    return redirect('/')->with('success', 'Password updated! Please login.');
}
}