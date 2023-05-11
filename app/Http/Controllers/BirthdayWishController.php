<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class BirthdayWishController extends Controller
{
    public function send(Request $request)
    {
        $name = $request->input('name');
        $birthday = $request->input('birthday');

        // TODO: Calculate the number of days until the user's birthday

        $factory = (new Factory)->withServiceAccount(__DIR__.'/firebase.php');
        $messaging = $factory->createMessaging();

        $message = CloudMessage::withTarget('token', $request->input('device_token'))
            ->withNotification(Notification::create('Happy Birthday!', "It's $name's birthday today!"))
            ->withData(['type' => 'birthday', 'name' => $name, 'birthday' => $birthday]);

        $messaging->send($message);

        return response()->json(['message' => 'Birthday wish sent.']);
    }
}
