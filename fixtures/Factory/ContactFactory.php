<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures\Factory;

use Dogstronauts\AstroBook\Shared\Contacts\Model\Contact;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @internal
 *
 * @extends PersistentProxyObjectFactory<Contact>
 */
final class ContactFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Contact::class;
    }

    protected function defaults(): array
    {
        return [
            'firstname' => self::faker()->firstName,
            'lastname' => self::faker()->lastName,
            'email' => self::faker()->email,
            'phone' => self::faker()->phoneNumber,
            'address' => AddressFactory::new(),
        ];
    }
}
