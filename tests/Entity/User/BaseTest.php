<?php

namespace App\Tests\TestCase\Entity;

use App\Entity\Application;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    /** @param User */
    private $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testUserHasSetRole(): void
    {
        $this->assertTrue(method_exists($this->user, 'setRole'));
    }

    /**
     * @test
     * @dataProvider provideUserData
     */
    public function testUserCanSetRole($username, $role): void
    {
        $this->user->setRole($role);
        $this->assertEquals($role, $this->user->getRole());
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
        // [[$username, $role]]
        return [
            [
                'test@test.com',
                'ROLE_ADMIN',
            ],
            [
                'test@test.com',
                'ROLE_MODERATOR',
            ],
            [
                'test@test.com',
                'ROLE_USER',
            ],
        ];
    }
}
