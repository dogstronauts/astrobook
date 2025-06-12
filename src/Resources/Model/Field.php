<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Resources\Model;

use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Defines a custom field for resources in the AstroBook platform.
 *
 * Fields are the building blocks used to capture and display information in the system.
 * Each field has a specific data type (like text, number, date), a display name,
 * and optional validation rules to ensure data quality.
 *
 * @see ResourceType
 * @see FieldType
 * @see FieldConstraint
 */
class Field
{
    #[Serializer\Groups(['resource-type:read', 'resource-type:write'])]
    #[Assert\NotBlank]
    public FieldType $type;

    #[Serializer\Groups(['resource-type:read', 'resource-type:write'])]
    #[Assert\NotBlank]
    public string $label;

    /** @var list<FieldConstraint> */
    #[Serializer\Groups(['resource-type:read', 'resource-type:write'])]
    #[Assert\Valid]
    public array $constraints = [];
}
