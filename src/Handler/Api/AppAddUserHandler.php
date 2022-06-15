<?php

namespace App\Handler\Api;

use App\Entity\Application;
use App\Handler\Api\AppHandlerInterface;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;

class AppAddUserHandler implements AppHandlerInterface
{
    private $em;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    public function handle(Application $data): Application
    {
        $user = $data->getUser();

        /** @var ApplicationRepository $repo */
        $repo = $this->em->getRepository(Application::class);

        if ($repo->hasUser($data, $user)) {
            return $data;
        }

        $data->addUser($user);
        return $data;
    }
}
