<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function showLoginForm()
    {
        if (session('user_id')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function showRegisterForm()
    {
        if (session('user_id')) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $name = strtok($validated['email'], '@') ?: 'User';

        User::create([
            'name' => $name,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('login')
            ->with('success', 'Registration successful. Please sign in.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Invalid credentials.',
                'password' => 'Invalid credentials.',
            ]);
        }

        // Regenerate the session to prevent session-fixation attacks
        $request->session()->regenerate();

        session([
            'user_id'  => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Login successful.');
    }

    public function googleRedirect()
    {
        return redirect()->route('login')
            ->withErrors(['email' => 'Google sign-in is not configured yet.']);
    }

    public function showSettingsForm()
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));

        if (! $user) {
            return redirect()->route('login');
        }

        return view('pages.setting', [
            'user' => $user,
        ]);
    }

    public function updateSettings(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));

        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['required', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        session([
            'email' => $user->email,
        ]);

        return redirect()->route('settings')
            ->with('success', 'Settings updated successfully.');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have logged out successfully.');
    }
}