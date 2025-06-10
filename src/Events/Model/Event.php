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
use Dogstronauts\AstroBook\Events\Enum\EventStatus;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a event ressource in the application.
 *
 * Used to manage events who access the platform,
 * with permissions defined through configurable roles.
 */
#[ORM\Entity]
#[ORM\Table(name: '`event`')]
#[ApiResource(
    normalizationContext: ['groups' => ['event:read']],
    denormalizationContext: ['groups' => ['event:write']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            security: "is_granted('ROLE_PLATFORM')",
            securityMessage: 'Only users with ROLE_PLATFORM can create events.'
        ),
        new Patch(
            security: "is_granted('ROLE_PLATFORM')",
            securityMessage: 'Only users with ROLE_PLATFORM can update events.'
        ),
        new Delete(
            security: "is_granted('ROLE_PLATFORM')",
            securityMessage: 'Only users with ROLE_PLATFORM can delete events.'
        ),
    ],
)]
#[Assert\Expression(
    "this.getStartAt() < this.getEndAt()",
    message: "The start date must be before the end date."
)]
class Event
{
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME)]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    #[ORM\GeneratedValue('CUSTOM')]
    #[Serializer\Groups(['event:read'])]
    public Ulid $id;

    #[ORM\Column(type: Types::STRING, length: 128)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 128)]
    #[Serializer\Groups(['event:read', 'event:write'])]
    public string $label;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Serializer\Groups(['event:read', 'event:write'])]
    public ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull]
    #[Assert\Type(\DateTimeInterface::class)]
    #[Serializer\Groups(['event:read', 'event:write'])]
    public \DateTimeImmutable $startAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull]
    #[Assert\Type(\DateTimeInterface::class)]
    #[Serializer\Groups(['event:read', 'event:write'])]
    public \DateTimeImmutable $endAt;

    #[ORM\Column(enumType: EventStatus::class)]
    #[Assert\Choice(callback: [EventStatus::class, 'cases'])]
    #[Serializer\Groups(['event:read', 'event:write'])]
    public EventStatus $status = EventStatus::Draft;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[Serializer\Groups(['event:read'])]
    public int $duration = 0;

    public function getStartAt(): \DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): self
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function getEndAt(): \DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): self
    {
        $this->endAt = $endAt;
        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * Duration in minutes
     */
    private function updateDuration(): void
    {
        if (
            isset($this->startAt) && $this->startAt instanceof \DateTimeImmutable &&
            isset($this->endAt) && $this->endAt instanceof \DateTimeImmutable
        ) {
            $this->duration = max(0, (int)(($this->endAt->getTimestamp() - $this->startAt->getTimestamp()) / 60));
        } else {
            $this->duration = 0;
        }
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function lifecycleUpdateDuration(): void
    {
        $this->updateDuration();
    }
}
