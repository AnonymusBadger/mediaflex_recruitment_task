<?php

namespace App\Controller\Api;

use App\Entity\Application;
use App\Handler\Api\AppAddUserHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AppAddUserController extends AbstractController
{
    private $appAddUserHandler;

    public function __construct(AppAddUserHandler $appAddUserHandler)
    {
        $this->appAddUserHandler = $appAddUserHandler;
    }

    public function __invoke(Application $data): Application
    {
        $this->appAddUserHandler->handle($data);

        return $data;
    }
}
