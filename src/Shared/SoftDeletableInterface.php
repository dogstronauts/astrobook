<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared;

/**
 * Interface for entities that support soft deletion.
 *
 * Entities implementing this interface can be marked as deleted without
 * being physically removed from the database, supporting audit use cases
 * and preventing irreversible data loss.
 */
interface SoftDeletableInterface
{
    public ?\DateTimeImmutable $deletedAt {
        get;
    }
}
