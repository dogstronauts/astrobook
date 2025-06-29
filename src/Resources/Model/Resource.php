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
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a resource within the AstroBook platform.
 *
 * A resource refers to any reservable asset, such as "Command Module #3",
 * "Orbital Lab A", or "Hydroponic Chamber 7B". Each resource is linked to a
 * ResourceType that defines its expected structure and behavior.
 */
#[ORM\Entity]
#[ApiMetadata\ApiResource(
    normalizationContext: ['groups' => ['resource:read']],
    denormalizationContext: ['groups' => ['resource:write']],
    security: 'is_granted("ROLE_PLATFORM")'
)]
class Resource
{
    /**
     * Unique identifier used to track the resource.
     */
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME)]
    #[ORM\GeneratedValue('CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    #[Serializer\Groups(['resource:read'])]
    public Ulid $id;

    /**
     * Defines the type this resource belongs to (e.g. "Orbital Lab", "EVA Simulator").
     *
     * The type determines how the resource is configured and what rules apply to it.
     */
    #[ORM\ManyToOne(targetEntity: ResourceType::class)]
    #[Assert\NotBlank]
    #[Serializer\Groups(['resource:read', 'resource:write'])]
    #[ApiMetadata\ApiProperty(readableLink: false)]
    public ResourceType $type;

    /**
     * Short name used to identify the resource in listings and interfaces.
     *
     * Example: "Orbital Lab A", "Chamber 12", "Docking Port B2".
     */
    #[ORM\Column(type: Types::STRING, length: 64)]
    #[Serializer\Groups(['resource:read', 'resource:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    public string $label;

    /**
     * Optional operational or contextual information about the resource.
     *
     * Can include usage instructions, status notes, location details, and so on.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Serializer\Groups(['resource:read', 'resource:write'])]
    public ?string $description = null;
}
