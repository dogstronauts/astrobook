<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Auth;

use Doctrine\ORM\EntityManagerInterface;
use Dogstronauts\AstroBook\Auth\Model\PasswordResetRequest;
use Dogstronauts\AstroBook\Auth\RemoteEvent\PasswordResetConfirmWebhookConsumer;
use Dogstronauts\AstroBook\Auth\Repository\PasswordResetRequestRepository;
use Dogstronauts\AstroBook\Fixtures\Factory\PasswordResetRequestFactory;
use Dogstronauts\AstroBook\Tests\Shared\Functional\KernelTestCase;
use Dogstronauts\AstroBook\Users\Model\User;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Uid\Ulid;

/**
 * @internal
 */
#[Group('auth')]
#[Group('webhook')]
final class PasswordResetConfirmWebhookConsumerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private PasswordResetRequestRepository $passwordResetRequestRepository;

    private PasswordHasherFactoryInterface $passwordHasherFactory;

    private PasswordResetConfirmWebhookConsumer $consumer;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;

        /** @var PasswordResetRequestRepository $passwordResetRequestRepository */
        $passwordResetRequestRepository = self::getContainer()->get(PasswordResetRequestRepository::class);
        $this->passwordResetRequestRepository = $passwordResetRequestRepository;

        /** @var PasswordHasherFactoryInterface $passwordHasherFactory */
        $passwordHasherFactory = self::getContainer()->get(PasswordHasherFactoryInterface::class);
        $this->passwordHasherFactory = $passwordHasherFactory;

        /** @var PasswordResetConfirmWebhookConsumer $consumer */
        $consumer = self::getContainer()->get(PasswordResetConfirmWebhookConsumer::class);
        $this->consumer = $consumer;
    }

    public function testConsumeUpdatesPasswordAndDeletesRequest(): void
    {
        $user = $this->createUser(identifier: 'reset@example.com');

        $newPassword = 'N3wStr0ngP@ss!';

        $passwordRequest = $this->createPasswordRequest($user, $newPassword, new \DateTimeImmutable('+20 minutes'));

        $requestId = $passwordRequest->id;

        $event = new RemoteEvent(
            'password.reset.confirmed',
            $passwordRequest->token->toString(),
            ['resetToken' => $passwordRequest->token->toString()],
        );

        $this->consumer->consume($event);
        $this->entityManager->clear();

        $updatedUser = $this->entityManager->getRepository(User::class)->find($user->id);
        self::assertNotNull($updatedUser);
        self::assertTrue(
            $this->passwordHasherFactory->getPasswordHasher(User::class)->verify($updatedUser->password, $newPassword)
        );
        self::assertNull($this->passwordResetRequestRepository->find($requestId));
    }

    public function testConsumeKeepsRequestWhenTokenDoesNotMatch(): void
    {
        $user = $this->createUser(identifier: 'ignore@example.com');
        $newPassword = 'An0therStr0ng!';
        PasswordResetRequestFactory::createOne([
            'user' => $user,
            'identifier' => $user->identifier,
            'token' => new Ulid(Ulid::generate()),
            'password' => $this->passwordHasherFactory->getPasswordHasher(User::class)->hash($newPassword),
        ]);
        $passwordRequest = $this->createPasswordRequest(
            $user,
            $newPassword,
            new \DateTimeImmutable('+20 minutes'),
        );

        $originalPasswordHash = $user->password;

        $event = new RemoteEvent(
            'password.reset.confirmed',
            $resetToken = Ulid::generate(),
            ['resetToken' => $resetToken],
        );

        $this->consumer->consume($event);
        $this->entityManager->clear();

        $unchangedUser = $this->entityManager->getRepository(User::class)->find($user->id);
        self::assertNotNull($unchangedUser);
        self::assertSame($originalPasswordHash, $unchangedUser->password);

        $unchangedRequest = $this->passwordResetRequestRepository->find($passwordRequest->id);
        self::assertNotNull($unchangedRequest);
    }

    private function createPasswordRequest(User $user, string $plainPassword, \DateTimeImmutable $expiresAt): PasswordResetRequest
    {
        $passwordRequest = new PasswordResetRequest();
        $passwordRequest->user = $user;
        $passwordRequest->identifier = $user->identifier;
        $passwordRequest->token = new Ulid(Ulid::generate($expiresAt));
        $passwordRequest->password = $this->passwordHasherFactory->getPasswordHasher(User::class)->hash($plainPassword);
        $passwordRequest->expiresAt = $expiresAt;

        $this->entityManager->persist($passwordRequest);
        $this->entityManager->flush();

        return $passwordRequest;
    }
}
