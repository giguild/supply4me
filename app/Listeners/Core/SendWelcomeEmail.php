<?php

namespace App\Listeners\Core;

use App\Events\Core\UserCreated;
use App\Models\Core\User;
use App\Mail\WelcomeUserMail;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    public function handle(UserCreated $event): void
    {
        /** @var User $user */
        $user = $event->user;

        $user->load('roles');

        if ($user->email) {
            Mail::to($user->email)->send(new WelcomeUserMail(
                $user,
                $event->password,
                $user->roles->pluck('name')->toArray(),
            ));
        }
    }
}