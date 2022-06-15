<?php

namespace App\Tests\Application\User;

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
        $this->assertTrue(method_exists($this->appsRepo, 'getRole'));

        $roles = $this->appsRepo->getRole('AppWithMany', 'user@user.com');
        $this->assertContains('ROLE_USER', $roles);

        $roles = $this->appsRepo->getRole('AppWithMany', 'moderator@user.com');
        $this->assertContains('ROLE_MODERATOR', $roles);

        $roles = $this->appsRepo->getRole('AppWithMany', 'admin@user.com');
        $this->assertNull($roles);

        // Takes objects?
        $app = $this->appsRepo->findByName('AppWithMany');
        $user = $this->usersRepo->findByEmail('user@user.com');

        $roles = $this->appsRepo->getRole($app, $user);
        $this->assertContains('ROLE_USER', $roles);
    }

    public function testHasUser()
    {
        $this->assertTrue(method_exists($this->appsRepo, 'hasUser'));

        $app = $this->appsRepo->findByName('AppWithMany');
        $user = $this->usersRepo->findByEmail('user@user.com');
        $mod = $this->usersRepo->findByEmail('moderator@user.com');
        $admin = $this->usersRepo->findByEmail('admin@user.com');

        $res = $this->appsRepo->hasUser($app, $user);
        $this->assertTrue($res);

        $res = $this->appsRepo->hasUser($app, $mod);
        $this->assertTrue($res);

        $res = $this->appsRepo->hasUser($app, $admin);
        $this->assertFalse($res);
    }
}
