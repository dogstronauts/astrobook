<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures\Factory;

use Dogstronauts\AstroBook\Shared\Contacts\Model\Address;
use Zenstruck\Foundry\ObjectFactory;

/**
 * @internal
 *
 * @extends ObjectFactory<Address>
 */
final class AddressFactory extends ObjectFactory
{
    public static function class(): string
    {
        return Address::class;
    }

    protected function defaults(): array
    {
        return [
            'street' => self::faker()->streetAddress,
            'city' => self::faker()->city,
            'postalCode' => self::faker()->postcode,
            'country' => self::faker()->country,
        ];
    }
}
