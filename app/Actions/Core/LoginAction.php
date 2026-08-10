<?php

namespace App\Actions\Core;

use App\Events\Core\UserCreated;
use App\Models\Core\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginAction
{
    public function execute(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new \App\Exceptions\AuthenticationException('Invalid credentials.');
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
