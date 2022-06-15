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

                $client->request('PATCH', $appAddUserUrl, [
                        'headers' => ['Content-Type' => 'application/merge-patch+json'],
                        'json' => [
                                'user' => $userData['@id'],
                        ]
                ]);

                $this->assertResponseIsSuccessful();
        }

        public function testCheckAccess(): void
        {
                $token = $this->getToken('admin@user.com');
                $client = $this->createClientWithCredentials($token);

                // Create user1
                $client->request('POST', 'api/users', [
                        'json' => [
                                'email' => 'user1@user.com',
                                'password' => 'pass'
                        ]
                ]);
                $this->assertResponseIsSuccessful();

                $user1Data = $client->getResponse()->toArray();

                // Create user2
                $client->request('POST', 'api/users', [
                        'json' => [
                                'email' => 'user2@user.com',
                                'password' => 'pass',
                                'roles' => ['ROLE_MODERATOR']
                        ]
                ]);
                $this->assertResponseIsSuccessful();

                $user2Data = $client->getResponse()->toArray();

                // Create app
                $client->request('POST', 'api/applications', [
                        'json' => [
                                'name' => 'foo',
                        ]
                ]);
                $this->assertResponseIsSuccessful();

                $appData = $client->getResponse()->toArray();

                // Add users to app
                $appAddUserUrl = 'api/applications/' . $appData['id'] . '/add_user';

                $client->request('PATCH', $appAddUserUrl, [
                        'headers' => ['Content-Type' => 'application/merge-patch+json'],
                        'json' => [
                                'user' => $user1Data['@id'],
                        ]
                ]);
                $this->assertResponseIsSuccessful();

                $client->request('PATCH', $appAddUserUrl, [
                        'headers' => ['Content-Type' => 'application/merge-patch+json'],
                        'json' => [
                                'user' => $user2Data['@id'],
                        ]
                ]);
                $this->assertResponseIsSuccessful();

                // Get role normal user
                $appHasAccessUrl = 'api/applications/' . $appData['id'] . '/user_has_access';

                $client->request('POST', $appHasAccessUrl, [
                        'json' => [
                                'email' => $user1Data['email'],
                        ]
                ]);
                $this->assertResponseIsSuccessful();

                $this->assertJsonContains([
                        'hasAccess' => true,
                        'roles' => ['ROLE_USER']
                ]);

                // Get role moderator user
                $client->request('POST', $appHasAccessUrl, [
                        'json' => [
                                'email' => $user2Data['email'],
                        ]
                ]);
                $this->assertResponseIsSuccessful();

                $this->assertJsonContains([
                        'hasAccess' => true,
                        'roles' => ['ROLE_MODERATOR']
                ]);

                // Get role no user
                $client->request('POST', $appHasAccessUrl, [
                        'json' => [
                                'email' => 'foo',
                        ]
                ]);
                $this->assertResponseIsSuccessful();
                $this->assertJsonContains([
                        'hasAccess' => false,
                ]);
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
