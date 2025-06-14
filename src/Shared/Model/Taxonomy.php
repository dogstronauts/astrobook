<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared\Model;

use ApiPlatform\Metadata as ApiMetadata;
use ApiPlatform\OpenApi\Model\Operation;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a taxonomy within AstroBook.
 *
 * Taxonomies are used to categorize models in a hierarchical structure,
 * allowing for organization and classification of content throughout the platform.
 */
#[ORM\Entity]
#[DoctrineAssert\UniqueEntity(fields: ['label'])]
#[ApiMetadata\ApiResource(
    operations: [
        new ApiMetadata\GetCollection(),
        new ApiMetadata\Get(),
        new ApiMetadata\Post(security: "is_granted('ROLE_PLATFORM')"),
        new ApiMetadata\Patch(security: "is_granted('ROLE_PLATFORM')"),
        new ApiMetadata\Delete(security: "is_granted('ROLE_PLATFORM')"),
    ],
    normalizationContext: ['groups' => ['taxonomy:read']],
    denormalizationContext: ['groups' => ['taxonomy:write']],
    openapi: new Operation(tags: ['Taxonomy', 'Shared']),
)]
class Taxonomy
{
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME)]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    #[ORM\GeneratedValue('CUSTOM')]
    #[Serializer\Groups(['taxonomy:read'])]
    public Ulid $id;

    #[ORM\Column(type: Types::STRING, length: 32, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 32)]
    #[Serializer\Groups(['taxonomy:read', 'taxonomy:write'])]
    public string $label;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Serializer\Groups(['taxonomy:read', 'taxonomy:write'])]
    public ?string $description = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Serializer\Groups(['taxonomy:read', 'taxonomy:write'])]
    #[ApiMetadata\ApiProperty(readableLink: false)]
    public ?self $parent = null;
}
