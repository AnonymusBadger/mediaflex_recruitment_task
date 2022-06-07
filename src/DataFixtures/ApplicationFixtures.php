<?php

namespace App\DataFixtures;

use App\Entity\Application;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ApplicationFixtures extends Fixture implements DependentFixtureInterface
{
    public const WITH_ADMIN_REFERENCE = 'with-admin';
    public const WITH_MODERATOR_REFERENCE = 'with-moderator';
    public const WITH_USER_REFERENCE = 'with-user';
    public const WITHOUT_ANY_REFERENCE = 'without-any';

    public function load(ObjectManager $manager)
    {
        $withAdmin = new Application();
        $withAdmin->setName('AppWithAdmin');
        $withAdmin->addUser($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
        $manager->persist($withAdmin);

        $withModerator = new Application();
        $withModerator->setName('AppWithModerator');
        $withModerator->addUser($this->getReference(UserFixtures::MODERATOR_USER_REFERENCE));
        $manager->persist($withModerator);

        $withUser = new Application();
        $withUser->setName('AppWithUser');
        $withUser->addUser($this->getReference(UserFixtures::USER_USER_REFERENCE));
        $manager->persist($withUser);

        $without = new Application();
        $without->setName('AppWithoutAny');
        $manager->persist($without);

        $manager->flush();

        $this->addReference(self::WITH_ADMIN_REFERENCE, $withModerator);
        $this->addReference(self::WITH_MODERATOR_REFERENCE, $withModerator);
        $this->addReference(self::WITH_USER_REFERENCE, $withUser);
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
