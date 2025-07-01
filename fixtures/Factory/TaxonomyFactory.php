<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures\Factory;

use Dogstronauts\AstroBook\Shared\Taxonomies\Model\Taxonomy;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @internal
 *
 * @extends PersistentProxyObjectFactory<Taxonomy>
 */
class TaxonomyFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Taxonomy::class;
    }

    protected function defaults(): array
    {
        return [
            'label' => self::faker()->text(32),
            'description' => self::faker()->paragraph(),
            'parent' => null,
        ];
    }
}
