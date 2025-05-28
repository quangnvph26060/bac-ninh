<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::routes([
    'middleware' => ['web', 'auth:admin,web']
]);

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.Employee.{id}', function ($employee, $id) {
    return Auth::guard('admin')->check() && $employee->id == $id;
});

// Dành cho user (web guard)
Broadcast::channel('chat.User.{id}', function ($user, $id) {
    return Auth::guard('web')->check() && $user->id == $id;
});


