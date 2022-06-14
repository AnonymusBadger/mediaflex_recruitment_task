<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\DatabasePrimer;

class AuthenticationTest extends ApiTestCase
{
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);
    }

    public function testLogin(): void
    {
        $client = self::createClient();

        // retrieve a token
        $response = $client->request('POST', '/api/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@user.com',
                'password' => 'pass',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // invalid credentials
        $client->request('POST', '/api/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'no@user.com',
                'password' => 'pass',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);

        // not authorized
        $client->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(401);

        // authorized
        $client->request('GET', '/api/users', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }
}
