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

interface UserFactoryInterface
{
    public function createUser(string $identifier, string $plainPassword, array $roles): User;
}
