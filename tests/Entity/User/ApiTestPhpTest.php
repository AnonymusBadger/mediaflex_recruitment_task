<?php

namespace App\Tests\Entity\User;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\DatabasePrimer;

class ApiTestPhpTest extends ApiTestCase
{
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);
    }

    // public function testItsExposedAsUsers(): void
    // {
    //     $response = static::createClient()->request('GET', '/api/users');

    //     $this->assertResponseIsSuccessful();
    //     $this->assertJsonContains(['@id' => '/api/users']);
    // }

    public function testItHasFirewall(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/users');

        $this->assertResponseStatusCodeSame(401);
    }
}
