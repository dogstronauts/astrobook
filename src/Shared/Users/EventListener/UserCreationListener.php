<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared\Users\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Dogstronauts\AstroBook\Shared\Users\Event\UserCreationEvent;
use Dogstronauts\AstroBook\Shared\Users\Exception;
use Dogstronauts\AstroBook\Shared\Users\UserFactoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * Listener for user creation events.
 *
 * Handles user creation by intercepting UserCreationEvent and delegating
 * the actual creation and validation to the UserBuilder service.
 */
#[AsEventListener(event: UserCreationEvent::class)]
final readonly class UserCreationListener
{
    public function __construct(
        private UserFactoryInterface $userFactory,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(UserCreationEvent $event): void
    {
        $user = $this->userFactory->createUser($event->identifier, $event->plainPassword, $event->roles);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (\Throwable $throwable) {
            throw Exception::persistenceFailed($throwable->getMessage(), $throwable);
        }
    }
}
