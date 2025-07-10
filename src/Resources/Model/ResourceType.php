<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Resources\Model;

use ApiPlatform\Metadata as ApiMetadata;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Dogstronauts\AstroBook\Fields\Model\Field;
use Dogstronauts\AstroBook\Shared\SoftDeletableInterface;
use Dogstronauts\AstroBook\Shared\SoftDeletableTrait;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a resource type within the application.
 *
 * This class defines the structure and attributes that a resource of this type must have.
 * It allows for dynamic configuration of resource properties based on their type.
 */
#[ORM\Entity]
#[ApiMetadata\ApiResource(
    normalizationContext: ['groups' => ['resource-type:read']],
    denormalizationContext: ['groups' => ['resource-type:write']],
    security: 'is_granted("ROLE_PLATFORM")'
)]
class ResourceType implements SoftDeletableInterface
{
    use SoftDeletableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME)]
    #[ORM\GeneratedValue('CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    #[Serializer\Groups(['resource-type:read'])]
    public Ulid $id;

    #[ORM\Column(type: Types::STRING, length: 32)]
    #[Serializer\Groups(['resource-type:read', 'resource-type:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    public string $label;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Serializer\Groups(['resource-type:read', 'resource-type:write'])]
    public ?string $description = null;

    /** @var list<Field> */
    #[ORM\Column(type: Types::JSON)]
    #[Serializer\Groups(['resource-type:read', 'resource-type:write'])]
    #[Assert\Valid]
    public array $fields = [];
}
