<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared\Users;

use Dogstronauts\AstroBook\Shared\Users\Model\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class UserFactory implements UserFactoryInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator,
    ) {
    }

    public function createUser(string $identifier, #[\SensitiveParameter] string $plainPassword, array $roles): User
    {
        $user = new User();
        $user->identifier = $identifier;
        $user->plainPassword = $plainPassword;
        $user->roles = $roles;

        $violations = $this->validator->validate($user);
        if (count($violations) > 0) {
            throw Exception::validationFailed($violations);
        }

        $user->password = $this->passwordHasher->hashPassword($user, $user->plainPassword);

        return $user;
    }
}
