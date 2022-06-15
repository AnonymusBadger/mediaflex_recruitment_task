<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Tests\DatabasePrimer;

abstract class CustomApiTestCase extends ApiTestCase
{
    public function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);
    }

    protected function createClientWithCredentials($token = null): Client
    {
        $token = $token ? $token : $this->getToken();

        return static::createClient([], ['auth_bearer' => $token]);
    }

    /**
     * Use other credentials if needed.
     */
    protected function getToken($email = 'user@user.com', $password = 'pass', $client = null): string
    {
        if (!$client) $client = static::createClient();

        $response = $client->request(
            'POST',
            '/api/login',
            ['json' => [
                'email' => $email,
                'password' => $password,
            ]]
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent());

        return $data->token;
    }
}
