<?php

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterGuestAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data): User
    {
        $password = $data['password'];
        if (! is_string($password)) {
            throw new \InvalidArgumentException('Password must be a string.');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'role' => 'guest',
        ]);

        Auth::login($user);

        return $user;
    }
}
