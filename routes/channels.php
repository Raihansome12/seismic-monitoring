<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('seismic-data', function () {
    return true;
});

Broadcast::channel('gps-channel', function () {
    return true;
});