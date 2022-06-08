<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /** @var UserPasswordHasherInterface */
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->passwordHasher = $userPasswordHasherInterface;
    }
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::VIEW => ['hashPassword', EventPriorities::PRE_WRITE]];
    }

    public function hashPassword(ViewEvent $event): void
    {
        /** @var User $user */
        $user = $event->getControllerResult();

        if (!$user instanceof User || ($user instanceof User && $user->plainPassword === null)) {
            return;
        }

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $user->plainPassword)
        );
        $user->eraseCredentials();
    }
}
