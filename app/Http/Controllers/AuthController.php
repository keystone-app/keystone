<?php

namespace App\Http\Controllers;

use App\Domain\Identity\Actions\RegisterGuestAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request, RegisterGuestAction $action)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = $action->execute($data);
        $request->session()->regenerate();

        return response()->json([
            'user' => $user,
            'role' => $user->role,
            'csrf_token' => csrf_token(),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json([
                'user' => Auth::user(),
                'role' => Auth::user()->role,
                'csrf_token' => csrf_token(),
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [__('auth.failed')],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me()
    {
        $user = Auth::user();

        return response()->json([
            'user' => $user,
            'role' => $user?->role,
            'identity_document' => $user?->getIdentityDocument(),
        ]);
    }
}
