<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures\Factory;

use Dogstronauts\AstroBook\Resources\Model\Resource;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<resource>
 */
final class ResourceFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Resource::class;
    }

    protected function defaults(): array
    {
        return [
            'type' => ResourceTypeFactory::new()->create(),
            'label' => self::faker()->text(32),
            'description' => self::faker()->realText(),
        ];
    }
}
