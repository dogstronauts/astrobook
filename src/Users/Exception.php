<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Users;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final class Exception extends \RuntimeException
{
    public static function persistenceFailed(string $reason, ?\Throwable $previous = null): self
    {
        return new self(
            sprintf('Failed to create user: %s', $reason),
            0,
            $previous
        );
    }

    public static function validationFailed(ConstraintViolationListInterface $violationList): self
    {
        return new self(sprintf(
            'User validation failed: %s',
            $violationList
        ));
    }
}
