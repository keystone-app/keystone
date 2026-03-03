<?php

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterGuestAction
{
    public function execute(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'guest',
        ]);

        Auth::login($user);

        return $user;
    }
}
