<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Users\Event;

use Dogstronauts\AstroBook\Users\Model\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event dispatched when a user creation is requested.
 *
 * Contains all necessary data for user creation including identifier,
 * plain password, and roles to be handled by event listeners.
 */
final class UserCreationEvent extends Event
{
    public User $createdUser;

    public function __construct(
        public readonly string $identifier,
        public readonly string $plainPassword,
        public readonly array $roles,
    ) {
    }
}
