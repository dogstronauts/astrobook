<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures\Factory;

use Dogstronauts\AstroBook\Users\Model\ContactType;
use Dogstronauts\AstroBook\Users\Model\UserContact;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @internal
 *
 * @extends PersistentProxyObjectFactory<UserContact>
 */
final class UserContactFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return UserContact::class;
    }

    protected function defaults(): array
    {
        return [
            'type' => self::faker()->randomElement(ContactType::cases()),
            'user' => UserFactory::new(),
            'contact' => ContactFactory::new(),
        ];
    }
}
