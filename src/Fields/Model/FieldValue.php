<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fields\Model;

use Dogstronauts\AstroBook\Fields\Validator\Constraints\FieldOptions;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents the value assigned to a specific custom field within a resource type.
 *
 * A FieldValue pairs a field identifier (`name`) with its corresponding input (`value`).
 * This allows dynamic storage and validation of data across different resource types.
 *
 * Each value is linked to a defined field structure, typically managed by the resource type definition.
 * Validation constraints are applied to ensure that required values are not omitted.
 *
 * @see FieldOptions
 */
class FieldValue
{
    /**
     * The identifier of the custom field.
     * This corresponds to a field definition declared in the resource type.
     */
    #[Serializer\Groups(['resource:read', 'resource:write'])]
    #[Assert\NotBlank]
    public string $key;

    /**
     * The value provided for the custom field.
     * This is the user-defined content corresponding to the field's input.
     */
    #[Serializer\Groups(['resource:read', 'resource:write'])]
    #[Assert\NotBlank]
    public string $value;
}
