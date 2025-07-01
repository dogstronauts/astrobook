<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fields\Model;

/**
 * Defines the available data types for fields in resources.
 *
 * This enum represents all supported field types that can be used when defining
 * resource fields, ensuring type safety and consistent data handling throughout the application.
 */
enum FieldType: string
{
    case STRING = 'string';

    case INT = 'int';

    case FLOAT = 'float';

    case BOOL = 'bool';

    case ARRAY = 'array';

    case DATE = 'date';

    case DATETIME = 'datetime';

    case JSON = 'json';
}
