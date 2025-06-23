<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Auth\ApiPlatform\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Dogstronauts\AstroBook\Auth\Model\PasswordResetRequest;
use Dogstronauts\AstroBook\Shared\Users\Model\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Ulid;

/**
 * This processor handles user data for creation, update, and deletion operations.
 * For non-deletion operations, it hashes the user's plain password and erases credentials
 * before persisting the entity. For deletion operations, it delegates the removal to the
 * configured remove processor.
 *
 * @implements ProcessorInterface<User, User|void>
 */
final readonly class PasswordResetRequestProcessor implements ProcessorInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $urlGenerator,
        private MailerInterface $mailer,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof PasswordResetRequest);

        $user = $this->entityManager->getRepository(User::class)->findOneByIdentifier($data->identifier);

        if (!$user) {
            return;
        }

        $data->user = $user;
        $data->password = $this->passwordHasher->hashPassword($data->user, $data->confirmPassword);

        $tokenExpiredAt = new \DateTimeImmutable('+30 minutes');

        $data->token = new Ulid(Ulid::generate($tokenExpiredAt));
        $data->expiresAt = $tokenExpiredAt;

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        $confirmationLink = $this->urlGenerator->generate(
            '_webhook_controller',
            [
                'type' => 'password-reset/confirm',
                'reset_token' => $data->token,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = new TemplatedEmail()
            ->to($data->user->identifier)
            ->subject('Reset password request confirmation')
            ->htmlTemplate('auth/password_reset_request_confirm.html.twig')
            ->context(
                ['confirmationLink' => $confirmationLink],
            )
        ;

        $this->mailer->send($email);
    }
}
