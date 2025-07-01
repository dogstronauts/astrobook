<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures\Factory;

use Dogstronauts\AstroBook\Fields\Model\Field;
use Dogstronauts\AstroBook\Fields\Model\FieldType;
use Zenstruck\Foundry\ObjectFactory;

/**
 * @extends ObjectFactory<Field>
 */
final class FieldFactory extends ObjectFactory
{
    public static function class(): string
    {
        return Field::class;
    }

    protected function defaults(): array
    {
        return [
            'type' => self::faker()->randomElement(FieldType::cases()),
            'label' => self::faker()->text(),
        ];
    }
}
