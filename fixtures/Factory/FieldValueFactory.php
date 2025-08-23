<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures\Factory;

use Dogstronauts\AstroBook\Resources\Model\FieldValue;
use Zenstruck\Foundry\ObjectFactory;

/**
 * @extends ObjectFactory<FieldValue>
 */
final class FieldValueFactory extends ObjectFactory
{
    public static function class(): string
    {
        return FieldValue::class;
    }

    protected function defaults(): array
    {
        return [
            'name' => self::faker()->text,
            'value' => self::faker()->text,
        ];
    }
}
