<?php

namespace App\Providers;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $serviceAccountPath = base_path(env('FIREBASE_CREDENTIALS'));

        // Check if the file exists and is readable
        if (!file_exists($serviceAccountPath) || !is_readable($serviceAccountPath)) {
            throw new \Exception("The Firebase service account JSON file is missing or not readable: " . $serviceAccountPath);
        }

        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification($token, $title, $body, $data = [], $type = 'token')
    {
        $notification = Notification::create($title, $body);
        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($data);
            $result = $this->messaging->send($message);
            return response()->json($result);
        } catch (\Kreait\Firebase\Exception\Messaging\MessagingError $e) {
            throw new \Exception('MessagingError: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('Error: ' . $e->getMessage());
        }
    }
}
