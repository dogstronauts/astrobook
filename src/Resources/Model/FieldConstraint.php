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
 * Represents a validation constraint that can be applied to a field.
 *
 * This class defines a constraint with its validation class and configurable options
 * that can be attached to fields to enforce data integrity rules.
 */
class FieldConstraint
{
    #[Serializer\Groups(['resource-type:read', 'resource-type:write'])]
    #[Assert\NotBlank]
    public string $class;

    /** @var array<string, string> */
    #[Serializer\Groups(['resource-type:read', 'resource-type:write'])]
    public array $options = [];
}
