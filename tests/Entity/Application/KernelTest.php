<?php

namespace App\Tests\Entity\User;

use App\Entity\Application;
use App\Entity\User;
use App\Repository\ApplicationRepository;
use App\Repository\UserRepository;
use App\Tests\DatabasePrimer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class KernelTest extends KernelTestCase
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ApplicationRepository */
    private $appsRepo;

    /** @var UserRepository */
    private $usersRepo;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);

        $this->entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $this->appsRepo = $this->entityManager->getRepository(Application::class);
        $this->usersRepo = $this->entityManager->getRepository(User::class);
    }


    public function testItPersistsApp(): void
    {
        $this->assertNotNull($this->appsRepo->findOneBy(['name' => 'AppWithUser']));
    }

    public function testItCanFindAppByName(): void
    {
        $this->assertTrue(method_exists($this->appsRepo, 'findByName'));

        $result = $this->appsRepo->findByName('AppWithUser');
        $this->assertNotNull($result);
        $this->assertInstanceOf(Application::class, $result);

        $result = $this->appsRepo->findByName('bleh');
        $this->assertNull($result);
    }

    public function testItCanCheckIfAppHasUser()
    {
        $this->assertTrue(method_exists($this->appsRepo, 'appHasUser'));

        // Check if objects accepted
        $app = $this->appsRepo->findByName('AppWithUser');
        $user = $this->usersRepo->findOneBy(['email' => 'user@user.com']);

        $result = $this->appsRepo->appHasUser($app, $user);

        $this->assertSame([
            'hasAccess' => true,
            'role' => 'ROLE_USER'
        ], $result);

        // Check if strings accepted
        $result = $this->appsRepo->appHasUser('AppWithUser', 'user@user.com');

        $this->assertIsArray($result);
        $this->assertSame([
            'hasAccess' => true,
            'role' => 'ROLE_USER'
        ], $result);

        // Check user not found
        $result = $this->appsRepo->appHasUser('AppWithUser', 'blah');

        $this->assertIsArray($result);
        $this->assertSame([
            'hasAccess' => false,
            'role' => null
        ], $result);
    }
}
