<?php

namespace App\Handler\Api;

use App\Entity\Application;
use App\Handler\Api\AppHandlerInterface;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;

class AppUserHasAccessHandler implements AppHandlerInterface
{
    private $em;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    public function handle(Application $data): Application
    {
        $userEmail = $data->getEmail();

        /** @var ApplicationRepository $repo */
        $repo = $this->em->getRepository(Application::class);

        $roles = $repo->getRole($data, $userEmail);

        if ($roles) {
            $data->setHasAccess(true);
            $data->setRoles($roles);
        } else {
            $data->setHasAccess(false);
        }

        return $data;
    }
}
