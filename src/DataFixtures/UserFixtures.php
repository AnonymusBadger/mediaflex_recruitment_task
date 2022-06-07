<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const MODERATOR_USER_REFERENCE = 'moderator-user';
    public const USER_USER_REFERENCE = 'user-user';

    /** @var UserPasswordHasherInterface */
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->passwordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $users = [
            [
                'ref' => self::ADMIN_USER_REFERENCE,
                'email' => 'admin@user.com',
                'password' => 'pass',
                'roles' => ['ROLE_ADMIN'],
            ],
            [
                'ref' => self::MODERATOR_USER_REFERENCE,
                'email' => 'moderator@user.com',
                'password' => 'pass',
                'roles' => ['ROLE_MODERATOR'],
            ],
            [
                'ref' => self::USER_USER_REFERENCE,
                'email' => 'user@user.com',
                'password' => 'pass',
                'roles' => ['ROLE_USER'],
            ]
        ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));
            $manager->persist($user);

            $this->addReference($userData['ref'], $user);
        }

        $manager->flush();
    }
}
