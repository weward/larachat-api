<?php

use App\Models\ChatQueue;
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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Broadcast::channel('test-channel', function ($chat_queue_id, $user = null, $email = null) {
//     // if (!is_null($user)) {
//     //     return $user->id === ChatQueue::find($chat_queue_id)->user_id;
//     // } else if (!is_null($email)) {
//     //     return $email === ChatQueue::where('email', $email)->first()->email;
//     // }

//     return true;
// });