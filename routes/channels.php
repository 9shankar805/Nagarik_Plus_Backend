<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('users.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('call.{id}', function ($user, $id) {
    $call = \App\Models\Call::find($id);
    if (!$call) {
        return false;
    }
    return (int) $user->id === (int) $call->caller_id || (int) $user->id === (int) $call->receiver_id;
});
