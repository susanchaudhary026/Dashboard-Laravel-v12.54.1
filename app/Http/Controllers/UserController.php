<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'superadmin') {
            return redirect('/dashboard')->with('error', 'Unauthorized access.');
        }

        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, $id)
    {
        if (Auth::user()->role !== 'superadmin') {
            return back()->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'role' => 'required|in:user,admin,superadmin'
        ]);

        $user = User::findOrFail($id);
        
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->role = $request->role;
        $user->save();

        return back()->with('success', 'User role updated successfully!');
    }
}