<?php

namespace App\Controller\Api;

use App\Entity\Application;
use App\Handler\Api\AppUserHasAccessHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AppUserHasAccessController extends AbstractController
{
    private $handler;

    public function __construct(AppUserHasAccessHandler $appUserHasAccessHandler)
    {
        $this->handler = $appUserHasAccessHandler;
    }

    public function __invoke(Application $data)
    {
        $this->handler->handle($data);

        return $data;
    }
}
