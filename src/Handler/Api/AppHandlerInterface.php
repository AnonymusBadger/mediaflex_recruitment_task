<?php

namespace App\Handler\Api;

use App\Entity\Application;

interface AppHandlerInterface
{
    public function handle(Application $data): Application;
}
