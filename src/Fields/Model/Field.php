<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fields\Model;

use Dogstronauts\AstroBook\Fields\Validator\Constraints as ResourcesAssert;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

use function Symfony\Component\String\u;

/**
 * Defines a custom field for resources in the AstroBook platform.
 *
 * @see ResourceType
 * @see FieldType
 * @see FieldConstraint
 */
#[ResourcesAssert\FieldOptions]
class Field
{
    #[Serializer\Groups(['resource-type:read'])]
    public string $code {
        get => u($this->label)->ascii()->replaceMatches('/[^a-z0-9]+/i', '_')->trim('_')->lower()->toString();
    }

    #[Serializer\Groups(['resource-type:read', 'resource-type:write'])]
    #[Assert\NotBlank]
    public FieldType $type;

    #[Serializer\Groups(['resource-type:read', 'resource-type:write'])]
    #[Assert\NotBlank]
    public string $label;

    /** @var array<string, string> */
    #[Serializer\Groups(['resource-type:read', 'resource-type:write'])]
    public array $options = [];

    /** @var list<FieldConstraint> */
    #[Serializer\Groups(['resource-type:read', 'resource-type:write'])]
    public array $constraints = [];
}
