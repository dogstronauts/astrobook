<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute as Serializer;

/**
 * Trait implementing the SoftDeletableInterface.
 *
 * This trait provides a standard implementation of soft deletion functionality
 * that can be used by any entity implementing the SoftDeletableInterface.
 */
trait SoftDeletableTrait
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Serializer\Groups(['deleted-at:read'])]
    public ?\DateTimeImmutable $deletedAt = null;
}
