<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures\Factory;

use Dogstronauts\AstroBook\Auth\Model\PasswordResetRequest;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Ulid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<PasswordResetRequest>
 */
class PasswordResetRequestFactory extends PersistentObjectFactory
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct();
    }

    #[\Override]
    public static function class(): string
    {
        return PasswordResetRequest::class;
    }

    #[\Override]
    protected function defaults(): array
    {
        return [
            'user' => UserFactory::new(),
            'expiresAt' => $expiresAt = \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'token' => Ulid::generate($expiresAt),
            'confirmPassword' => self::faker()->password(maxLength: 128),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this->afterInstantiate(function (PasswordResetRequest $passwordResetRequest): void {
            $passwordResetRequest->password = $this->userPasswordHasher->hashPassword(
                $passwordResetRequest->user,
                $passwordResetRequest->confirmPassword
            );
        });
    }
}
