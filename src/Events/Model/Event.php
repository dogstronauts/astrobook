<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) Damien Lebon <damienlebon.ifpa@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Events\Model;

use ApiPlatform\Metadata as ApiMetadata;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Dogstronauts\AstroBook\Events\Enum\EventStatus;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Ulid;

/**
 * Represents a event ressource in the application.
 *
 * Used to manage events who access the platform,
 * with permissions defined through configurable roles.
 */
#[ORM\Entity]
#[ORM\Table(name: '`event`')]
#[ApiMetadata\ApiResource(
    normalizationContext: ['groups' => ['event:read']],
    denormalizationContext: ['groups' => ['event:write']],
    operations: [
        'get',
        'post' => [
            'security' => "is_granted('ROLE_PLATFORM')",
            'securityMessage' => 'Only users with ROLE_PLATFORM can create events.',
        ],
        'patch' => [
            'security' => "is_granted('ROLE_PLATFORM')",
            'securityMessage' => 'Only users with ROLE_PLATFORM can update events.',
        ],
        'delete' => [
            'security' => "is_granted('ROLE_PLATFORM')",
            'securityMessage' => 'Only users with ROLE_PLATFORM can delete events.',
        ],
    ],
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
    #[Serializer\Groups(['event:read', 'event:write'])]
    public int $duration;

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getStatus(): EventStatus
    {
        return $this->status;
    }

    public function setStatus(EventStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }
}
