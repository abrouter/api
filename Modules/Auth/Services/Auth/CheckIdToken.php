<?php
declare(strict_types=1);

namespace Modules\Auth\Services\Auth;

use Illuminate\Auth\Access\AuthorizationException;
use Modules\Auth\Services\Auth\DTO\AuthWithGoogleRequestDTO;
use Google\Client;

class CheckIdToken
{   
    /**
     * @var Client
     */
    private $googleClient;

    public function __construct(Client $client)
    {
        $this->googleClient = new Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
    }

    public function checkId(AuthWithGoogleRequestDTO $googleRequest)
    {
        try {
            $payload = $this->googleClient->verifyIdToken($googleRequest->getIdToken());
        } catch (\Throwable $e) {
            throw new AuthorizationException('Invalid google token');
        }

        return $payload;
    }
}
