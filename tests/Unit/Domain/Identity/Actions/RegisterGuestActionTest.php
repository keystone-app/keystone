<?php

namespace Tests\Unit\Domain\Identity\Actions;

use App\Domain\Identity\Actions\RegisterGuestAction;
use App\Domain\Identity\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterGuestActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_register_a_guest_user(): void
    {
        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secret123',
        ];

        $action = new RegisterGuestAction();
        $user = $action->execute($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Jane Doe', $user->name);
        $this->assertEquals('jane@example.com', $user->email);
        $this->assertEquals('guest', $user->role);
        $this->assertTrue(Hash::check('secret123', $user->password));
        
        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'role' => 'guest',
        ]);

        $this->assertTrue(auth()->check());
        $this->assertEquals($user->id, auth()->id());
    }

    public function test_it_throws_exception_if_password_is_not_a_string(): void
    {
        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 12345, // Invalid type
        ];

        $action = new RegisterGuestAction();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must be a string.');

        $action->execute($data);
    }
}
