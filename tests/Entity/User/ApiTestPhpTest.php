<?php

namespace App\Tests\Entity\User;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\DatabasePrimer;

class ApiTestPhpTest extends ApiTestCase
{
    /**
     * Create a client with a default Authorization header.
     *
     * @param string $username
     * @param string $password
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function createAuthenticatedClient($email = 'user@user.com', $password = 'pass')
    {
        $client = static::createClient();
        $client->request('POST', '/api/login_check', [
            'headers' => ['Content_Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' => $password,
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);

        $_ENV['HTTP_Authorization'] = sprintf('Bearer %s', $data['token']);

        return $client;
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);
    }

    public function testTest(): void
    {
        $this->markTestIncomplete();
    }
}
