<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Events\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Dogstronauts\AstroBook\Shared\Model\SoftDeletableInterface;
use Dogstronauts\AstroBook\Shared\Model\SoftDeletableTrait;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents an event ressource withing AstroBook.
 */
#[ORM\Entity]
#[ORM\Table(name: '`event`')]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(security: "is_granted('ROLE_PLATFORM')"),
        new Patch(security: "is_granted('ROLE_PLATFORM')"),
        new Delete(security: "is_granted('ROLE_PLATFORM')"),
    ],
    normalizationContext: ['groups' => ['event:read']],
    denormalizationContext: ['groups' => ['event:write']],
)]
class Event implements SoftDeletableInterface
{
    use SoftDeletableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME)]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    #[ORM\GeneratedValue('CUSTOM')]
    #[Serializer\Groups(['event:read'])]
    public Ulid $id;

    #[ORM\Column(type: Types::STRING, length: 64)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    #[Serializer\Groups(['event:read', 'event:write'])]
    public string $label;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Serializer\Groups(['event:read', 'event:write'])]
    public ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotBlank]
    #[Serializer\Groups(['event:read', 'event:write'])]
    public \DateTimeImmutable $startAt;

    #[ORM\Column(length: 9, enumType: EventStatus::class)]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [EventStatus::class, 'cases'])]
    #[Serializer\Groups(['event:read', 'event:write'])]
    public EventStatus $status = EventStatus::DRAFT;

    /**
     * @var int duration in minutes
     */
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Serializer\Groups(['event:read', 'event:write'])]
    public int $duration;

    /**
     * @var \DateTimeImmutable $endAt is dynamically computed by adding
     *                         the duration (in minutes) to the start date
     */
    #[Serializer\Groups(['event:read'])]
    public \DateTimeImmutable $endAt {
        get => $this->startAt->modify(sprintf('+%d minutes', $this->duration));
    }
}
