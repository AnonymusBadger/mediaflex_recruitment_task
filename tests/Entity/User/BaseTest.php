<?php

namespace App\Tests\TestCase\Entity;

use App\Entity\Application;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    /** @var User */
    private $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    /**
     * @test
     * @dataProvider provideUserData
     */
    public function testUserCanSetRole($username, $roles): void
    {
        $this->user->setRoles($roles);
        $this->assertEquals($roles, $this->user->getRoles());
    }

    public function testUserCanAddApplications(): void
    {
        $this->assertTrue(method_exists($this->user, 'addApplication'));
    }

    public function testUserCanHaveApplications(): void
    {
        $this->user->addApplication(new Application());
        $this->user->addApplication(new Application());

        $this->assertNotEmpty($this->user->getApplications());
    }

    public function provideUserData()
    {
        // [[$username, $roles[]]]
        return [
            [
                'test@test.com',
                [
                    'ROLE_ADMIN',
                    'ROLE_USER'
                ]
            ],
            [
                'test@test.com',
                [
                    'ROLE_MODERATOR',
                    'ROLE_USER'
                ]
            ],
            [
                'test@test.com',
                [
                    'ROLE_USER',
                ]
            ],
        ];
    }
}
