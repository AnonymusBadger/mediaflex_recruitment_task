<?php

namespace App\Tests\TestCase\Entity;

use App\Entity\Application;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    /** @param Application */
    private $app;

    protected function setUp(): void
    {
        $this->app = new Application();
    }

    public function testItCanGetUsers(): void
    {
        $this->assertTrue(method_exists($this->app, 'getUsers'));
    }

    public function testUserCanAddUsers(): void
    {
        $this->app->addUser(new User());
        $this->assertNotEmpty($this->app->getUsers());
    }

    public function testUserCanHaveManyUsers(): void
    {
        $this->app->addUser(new User());
        $this->app->addUser(new User());

        $this->assertCount(2, $this->app->getUsers());
    }
}
