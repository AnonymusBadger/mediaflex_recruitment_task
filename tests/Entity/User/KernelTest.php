<?php

namespace App\Tests\Entity\User;

use App\Entity\User;
use App\Tests\DatabasePrimer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class KernelTest extends KernelTestCase
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserRepository */
    private $usersRepo;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);

        $this->entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $this->usersRepo = $this->entityManager->getRepository(User::class);
    }

    public function testCanGetUserByEmail(): void
    {
        $this->assertTrue(method_exists($this->usersRepo, 'findByEmail'));

        $user = $this->usersRepo->findByEmail('user@user.com');
        $this->assertNotNull($user);

        $user = $this->usersRepo->findByEmail('bob');
        $this->assertNull($user);
    }
}
