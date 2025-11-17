<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Auth\RemoteEvent;

use Doctrine\ORM\EntityManagerInterface;
use Dogstronauts\AstroBook\Auth\Model\PasswordResetRequest;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Uid\Ulid;

#[AsRemoteEventConsumer('password-reset/confirm')]
final readonly class PasswordResetConfirmWebhookConsumer implements ConsumerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        if ('password.reset.confirmed' !== $event->getName()) {
            return;
        }

        $passwordResetRequest = $this->entityManager
            ->getRepository(PasswordResetRequest::class)
            ->findNotExpired(Ulid::fromString($event->getPayload()['resetToken']))
        ;
        if (!$passwordResetRequest) {
            return;
        }

        $user = $passwordResetRequest->user;
        $user->password = $passwordResetRequest->password;

        try {
            $this->entityManager->beginTransaction();
            $this->entityManager->remove($passwordResetRequest);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Throwable) {
            $this->entityManager->rollback();
        }
    }
}
