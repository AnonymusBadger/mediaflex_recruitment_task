<?php

namespace App\DataFixtures;

use App\Entity\Application;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ApplicationFixtures extends Fixture implements DependentFixtureInterface
{
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

        $withMany = new Application();
        $withMany->setName('AppWithMany');
        $withMany->addUser($this->getReference(UserFixtures::MODERATOR_USER_REFERENCE));
        $withMany->addUser($this->getReference(UserFixtures::USER_USER_REFERENCE));
        $manager->persist($withMany);

        $without = new Application();
        $without->setName('AppWithoutAny');
        $manager->persist($without);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
