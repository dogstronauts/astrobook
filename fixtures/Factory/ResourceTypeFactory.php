<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures\Factory;

use Dogstronauts\AstroBook\Resources\Model\ResourceType;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<ResourceType>
 */
final class ResourceTypeFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return ResourceType::class;
    }

    protected function defaults(): array
    {
        return [
            'label' => self::faker()->text(32),
            'description' => self::faker()->realText(),
            'fields' => FieldFactory::new()->many(3),
        ];
    }
}
