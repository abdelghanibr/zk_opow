<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RemoteApiService
{
    public function getPersonsByComplex(int $complexId)
    {
        $secret = config('services.remote.secret');
        $base   = rtrim(config('services.remote.base'), '/');

        // HMAC signature
        $sig = hash_hmac('sha256', (string)$complexId, $secret);

        return Http::timeout(30)
            ->acceptJson()
            ->get("{$base}/api/complex/{$complexId}/persons", [
                'sig' => $sig,
            ]);
    }
}
