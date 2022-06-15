<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDataPersister implements DataPersisterInterface
{
    private $em;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->em = $entityManagerInterface;
        $this->passwordHasher = $userPasswordHasherInterface;
    }

    public function supports($data): bool
    {
        return $data instanceof User;
    }

    /** @param User $data */
    public function persist($data): User
    {
        if ($data->plainPassword) {
            $data->setPassword(
                $this->passwordHasher->hashPassword($data, $data->plainPassword)
            );

            $data->eraseCredentials();
        }

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }

    public function remove($data)
    {
        $this->em->remove($data);
        $this->em->flush();
    }
}
