<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const MODERATOR_USER_REFERENCE = 'moderator-user';
    public const USER_USER_REFERENCE = 'user-user';

    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setEmail('admin@user.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('admin');
        $manager->persist($admin);

        $moderator = new User();
        $moderator->setEmail('moderator@user.com');
        $moderator->setRoles(['ROLE_MODERATOR']);
        $moderator->setPassword('moderator');
        $manager->persist($moderator);

        $user = new User();
        $user->setEmail('user@user.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('user');
        $manager->persist($user);

        $manager->flush();

        $this->addReference(self::ADMIN_USER_REFERENCE, $admin);
        $this->addReference(self::MODERATOR_USER_REFERENCE, $moderator);
        $this->addReference(self::USER_USER_REFERENCE, $user);
    }
}
