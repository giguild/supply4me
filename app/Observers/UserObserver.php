<?php

namespace App\Observers;

use App\Events\Core\UserCreated;
use App\Events\Core\UserDeactivated;
use App\Events\Core\UserUpdated;
use App\Models\Core\User;
use Spatie\Activitylog\Facades\ActivityLog;
use Spatie\Activitylog\LogOptions;

class UserObserver
{
    public function created(User $user): void
    {
        ActivityLog::event('User created')
            ->on($user)
            ->withProperties([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'company_id' => $user->company_id,
            ])
            ->log();

        UserCreated::dispatch($user);
    }

    public function updated(User $user): void
    {
        $changes = $user->getChanges();

        ActivityLog::event('User updated')
            ->on($user)
            ->withProperties([
                'user_id' => $user->id,
                'attributes' => $changes,
                'old' => $user->getOriginal(),
            ])
            ->log();

        if (isset($changes['status']) && $changes['status'] === 'inactive') {
            UserDeactivated::dispatch($user);
        } else {
            UserUpdated::dispatch($user);
        }
    }

    public function deleted(User $user): void
    {
        ActivityLog::event('User deleted')
            ->on($user)
            ->withProperties([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->log();
    }

    public function restoring(User $user): void
    {
        ActivityLog::event('User restoring')
            ->on($user)
            ->withProperties([
                'user_id' => $user->id,
            ])
            ->log();
    }

    public function restored(User $user): void
    {
        ActivityLog::event('User restored')
            ->on($user)
            ->withProperties([
                'user_id' => $user->id,
            ])
            ->log();
    }
}
