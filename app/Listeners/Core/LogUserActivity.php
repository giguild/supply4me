<?php

namespace App\Listeners\Core;

use App\Events\Core\UserCreated;
use App\Events\Core\UserDeactivated;
use App\Events\Core\UserUpdated;
use App\Models\Core\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Activitylog\Facades\Activity;

class LogUserActivity implements ShouldQueue
{
    public function handle(UserCreated|UserUpdated|UserDeactivated $event): void
    {
        /** @var User $user */
        $user = $event->user;

        $eventName = class_basename($event);
        $properties = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'event' => $eventName,
        ];

        Activity::tap(fn ($activity) => $activity->causedBy($user)->withProperties($properties))
            ->event('user.'.strtolower(str_replace('User', '', class_basename($event))))
            ->performedOn($user)
            ->log("User {$eventName}: {$user->name} ({$user->email})");
    }
}
