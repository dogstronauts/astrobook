<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Resources\Fields\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class FieldOptions extends Constraint
{
    public const string INVALID_FIELD_OPTIONS_ERROR = 'e25a5cf6-65de-4119-97fd-1f532785e57a';

    protected const array ERROR_NAMES = [
        self::INVALID_FIELD_OPTIONS_ERROR => 'INVALID_FIELD_OPTIONS_ERROR',
    ];

    public string $message = '{{ errorMessage }}';

    #[\Override]
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
