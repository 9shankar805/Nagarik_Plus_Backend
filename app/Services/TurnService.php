<?php

namespace App\Services;

class TurnService
{
    public function credentials()
    {
        $secret = env('TURN_CREDENTIAL', '');
        $ttl = 3600;

        $username = time() + $ttl;

        $password = base64_encode(
            hash_hmac(
                'sha1',
                $username,
                $secret,
                true
            )
        );

        return [
            'username' => (string)$username,
            'credential' => $password,
        ];
    }

    public function iceServers()
    {
        $turnServer = env('TURN_SERVER');
        $turnPort = env('TURN_PORT', '3478');

        $iceServers = [];

        if (empty($turnServer) || empty(env('TURN_CREDENTIAL'))) {
            $iceServers[] = [
                'urls' => [
                    'stun:stun.l.google.com:19302',
                    'stun:stun1.l.google.com:19302',
                ],
            ];
            return ['iceServers' => $iceServers];
        }

        $credentials = $this->credentials();

        $iceServers[] = [
            'urls' => [
                'stun:' . $turnServer . ':' . $turnPort,
            ],
        ];

        $iceServers[] = [
            'urls' => [
                'turn:' . $turnServer . ':' . $turnPort . '?transport=udp',
                'turn:' . $turnServer . ':' . $turnPort . '?transport=tcp',
            ],
            'username' => $credentials['username'],
            'credential' => $credentials['credential'],
        ];

        return ['iceServers' => $iceServers];
    }
}
