<?php

namespace App\Tests\Entity\Application;

use App\Test\CustomApiTestCase;

class ApiTestPhpTest extends CustomApiTestCase
{
        public function testCreateApplication(): void
        {
                // Anonymous
                $client = static::createClient();
                $client->request('POST', 'api/applications', [
                        'json' => [
                                'name' => 'foo',
                        ]
                ]);

                $this->assertResponseStatusCodeSame(401);

                // User
                $client = $this->createClientWithCredentials();
                $client->request('POST', 'api/applications', [
                        'json' => [
                                'name' => 'foo',
                        ]
                ]);

                $this->assertResponseStatusCodeSame(403);

                // Moderator
                $token = $this->getToken('moderator@user.com');
                $client = $this->createClientWithCredentials($token);
                $client->request('POST', 'api/applications', [
                        'json' => [
                                'name' => 'foo',
                        ]
                ]);

                $this->assertResponseStatusCodeSame(403);

                // Admin
                $token = $this->getToken('admin@user.com');
                $client = $this->createClientWithCredentials($token);
                $client->request('POST', 'api/applications', [
                        'json' => [
                                'name' => 'foo',
                        ]
                ]);

                $this->assertResponseIsSuccessful();
        }

        public function testUpdateApplication(): void
        {
        }

        public function testAddUser(): void
        {
                $token = $this->getToken('admin@user.com');
                $client = $this->createClientWithCredentials($token);

                // Create user
                $client->request('POST', 'api/users', [
                        'json' => [
                                'email' => 'user1@user.com',
                                'password' => 'pass'
                        ]
                ]);
                $this->assertResponseIsSuccessful();

                $userData = $client->getResponse()->toArray();

                // Create app
                $client->request('POST', 'api/applications', [
                        'json' => [
                                'name' => 'foo',
                        ]
                ]);
                $this->assertResponseIsSuccessful();

                $appData = $client->getResponse()->toArray();

                // Add user to app
                $appAddUserUrl = 'api/applications/' . $appData['id'] . '/add_user';

                $client->request('POST', $appAddUserUrl, [
                        'json' => [
                                'user' => $userData['@id'],
                        ]
                ]);

                $this->assertResponseIsSuccessful();
        }

        public function testGetApplication(): void
        {
                // Anonymous
                $client = static::createClient();

                $client->request('GET', 'api/applications');
                $this->assertResponseStatusCodeSame(401);

                $client->request('GET', 'api/applications/1');
                $this->assertResponseStatusCodeSame(401);

                // User
                $client = $this->createClientWithCredentials();

                $client->request('GET', 'api/applications');
                $this->assertResponseStatusCodeSame(403);

                $client->request('GET', 'api/applications/1');
                $this->assertResponseStatusCodeSame(403);

                // Moderator
                $token = $this->getToken('moderator@user.com');
                $client = $this->createClientWithCredentials($token);

                $client->request('GET', 'api/applications');
                $this->assertResponseIsSuccessful();

                $client->request('GET', 'api/applications/1');
                $this->assertResponseIsSuccessful();

                // Admin
                $token = $this->getToken('admin@user.com');
                $client = $this->createClientWithCredentials($token);

                $client->request('GET', 'api/applications');
                $this->assertResponseIsSuccessful();

                $client->request('GET', 'api/applications/1');
                $this->assertResponseIsSuccessful();
        }
}
