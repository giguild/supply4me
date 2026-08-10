<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Broadcast channels for the Supply4Me application.
|
*/

Broadcast::channel('App.Models.Core.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('company.{companyId}', function ($user, $companyId) {
    return $user->company_id === $companyId;
});

Broadcast::channel('company.{companyId}.orders', function ($user, $companyId) {
    return $user->company_id === $companyId;
});

Broadcast::channel('company.{companyId}.deliveries', function ($user, $companyId) {
    return $user->company_id === $companyId;
});

Broadcast::channel('company.{companyId}.inventory', function ($user, $companyId) {
    return $user->company_id === $companyId;
});

Broadcast::channel('company.{companyId}.payments', function ($user, $companyId) {
    return $user->company_id === $companyId;
});

Broadcast::channel('driver.{driverId}.location', function ($user, $driverId) {
    return $user->id === $driverId;
});

Broadcast::channel('driver.{driverId}.assignment', function ($user, $driverId) {
    return $user->id === $driverId;
});
