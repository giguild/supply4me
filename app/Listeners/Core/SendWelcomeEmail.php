<?php

namespace App\Listeners\Core;

use App\Events\Core\UserCreated;
use App\Models\Core\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    public function handle(UserCreated $event): void
    {
        /** @var User $user */
        $user = $event->user;

        if ($user->email) {
            Mail::to($user->email)->send(new \App\Mail\WelcomeUserMail($user));
        }
    }
}
