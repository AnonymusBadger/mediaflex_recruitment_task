<?php

namespace App\Tests\Entity\User;

use App\Test\CustomApiTestCase;

class ApiTestPhpTest extends CustomApiTestCase
{
        public function testCreateUser(): void
        {
                // Anonymous
                $client = static::createClient();
                $client->request('POST', 'api/users', [
                        'json' => [
                                'email' => 'user0@user.com',
                                'password' => 'pass',
                                'roles' => ['ROLE_ADMIN']
                        ]
                ]);

                $user0 = [
                        'id' => $client->getResponse()->toArray()['id'],
                        'creator_role' => 'anonymous'
                ];

                $this->assertResponseStatusCodeSame(201);

                // User
                $client = $this->createClientWithCredentials();

                $client->request('POST', 'api/users', [
                        'json' => [
                                'email' => 'user1@user.com',
                                'password' => 'pass',
                                'roles' => ['ROLE_ADMIN']
                        ]
                ]);

                $this->assertResponseStatusCodeSame(201);

                $user1 = [
                        'id' => $client->getResponse()->toArray()['id'],
                        'creator_role' => 'anonymous'
                ];

                // As moderator
                $token = $this->getToken('moderator@user.com');
                $client = $this->createClientWithCredentials($token);

                $client->request('POST', 'api/users', [
                        'json' => [
                                'email' => 'user2@user.com',
                                'password' => 'pass',
                                'roles' => ['ROLE_ADMIN']
                        ]
                ]);

                $user2 = [
                        'id' => $client->getResponse()->toArray()['id'],
                        'creator_role' => 'moderator'
                ];

                $this->assertResponseStatusCodeSame(201);

                // As admin
                $token = $this->getToken('admin@user.com');
                $client = $this->createClientWithCredentials($token);

                $client->request('POST', 'api/users', [
                        'json' => [
                                'email' => 'user3@user.com',
                                'password' => 'pass',
                                'roles' => ['ROLE_ADMIN']
                        ]
                ]);

                $user3 = [
                        'id' => $client->getResponse()->toArray()['id'],
                        'creator_role' => 'admin'
                ];

                // Only admin can set roles
                foreach ([$user0, $user1, $user2, $user3] as $user) {
                        $client->request('GET', 'api/users/' . $user['id']);

                        $data = $client->getResponse()->toArray();

                        if ($user['creator_role'] == 'admin') {
                                $this->assertSame(['ROLE_ADMIN', 'ROLE_USER'], $data['roles']);
                        } else {
                                $this->assertSame(['ROLE_USER'], $data['roles']);
                        }
                }


                // Missing params
                $client->request('POST', 'api/users', [
                        'json' => [
                                'email' => 'user2@user.com',
                        ]
                ]);

                $this->assertResponseStatusCodeSame(422);
                $this->assertJsonContains([
                        'hydra:description' => 'password: This value should not be blank.'
                ]);

                $client->request('POST', 'api/users', [
                        'json' => [
                                'password' => 'pass',
                        ]
                ]);

                $this->assertResponseStatusCodeSame(422);
                $this->assertJsonContains([
                        'hydra:description' => 'email: This value should not be blank.'
                ]);
        }

        public function testUpdateUser(): void
        {
                // As admin
                $token = $this->getToken('admin@user.com');
                $client = $this->createClientWithCredentials($token);

                // Create new user
                $res = $client->request('POST', 'api/users', [
                        'json' => [
                                'email' => 'user1@user.com',
                                'password' => 'pass',
                        ]
                ]);

                $json = $res->toArray();

                $this->assertResponseStatusCodeSame(201);
                $this->getToken('user1@user.com', 'pass', $client); // try logging in

                // Test it doesn't patch
                $res = $client->request('PATCH', 'api/users/' . $json['id'], [
                        'headers' => ['Content-Type' => 'application/merge-patch+json'],
                        'json' => [
                                'roles' => ['ROLE_MODERATOR'],
                                'password' => 'foo',
                                'email' => 'bar@user.com',
                                'applications' => [
                                        'api/applications/1'
                                ]
                        ]
                ]);

                $this->assertResponseStatusCodeSame(405);
        }

        public function testGetUser(): void
        {
                // Anonymous
                $client = static::createClient();
                $client->request('POST', 'api/users', [
                        'json' => [
                                'email' => 'user1@user.com',
                                'password' => 'pass',
                        ]
                ]);

                $this->assertResponseStatusCodeSame(201);

                $data = $client->getResponse()->toArray();
                $this->assertArrayNotHasKey('roles', $data);

                $client->request('GET', 'api/users/' . $data['id']);

                $this->assertResponseStatusCodeSame(401);

                $client->request('GET', 'api/users');

                $this->assertResponseStatusCodeSame(401);

                // User
                $token = $this->getToken('user1@user.com');
                $client = $this->createClientWithCredentials($token);

                // Get own data
                $client->request('GET', 'api/users/' . $data['id']);

                $data = $client->getResponse()->toArray();

                $this->assertResponseIsSuccessful();
                $this->assertArrayNotHasKey('roles', $data);

                // Create user other than self
                $client->request('POST', 'api/users', [
                        'json' => [
                                'email' => 'user2@user.com',
                                'password' => 'pass',
                        ]
                ]);

                $this->assertResponseStatusCodeSame(201);

                $data = $client->getResponse()->toArray();
                $this->assertArrayNotHasKey('roles', $data);

                // Get elses data
                $client->request('GET', 'api/users/' . $data['id']);

                $this->assertResponseStatusCodeSame(403);

                // Get users
                $client->request('GET', 'api/users');

                $this->assertResponseStatusCodeSame(403);


                // Moderator
                $token = $this->getToken('moderator@user.com');
                $client = $this->createClientWithCredentials($token);

                // Get elses data
                $client->request('GET', 'api/users/' . $data['id']);

                $this->assertResponseIsSuccessful();

                $data = $client->getResponse()->toArray();
                $this->assertArrayNotHasKey('roles', $data);

                // Get users
                $client->request('GET', 'api/users');

                $this->assertResponseIsSuccessful();

                // Admin
                $token = $this->getToken('admin@user.com');
                $client = $this->createClientWithCredentials($token);

                // Get elses data
                $client->request('GET', 'api/users/' . $data['id']);

                $this->assertResponseIsSuccessful();

                $data = $client->getResponse()->toArray();
                $this->assertArrayHasKey('roles', $data);

                // Get users
                $client->request('GET', 'api/users');

                $this->assertResponseIsSuccessful();
        }
}
