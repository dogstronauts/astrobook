<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Resources\Fields\Validator\Constraints;

use Dogstronauts\AstroBook\Resources\Fields\FieldOptionsResolverChain;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class FieldOptionsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly FieldOptionsResolverChain $resolverChain
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof FieldOptions) {
            throw new UnexpectedTypeException($constraint, FieldOptions::class);
        }

        try {
            $this->resolverChain->resolve($value);
        } catch (\Throwable $throwable) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ errorMessage }}', $throwable->getMessage())
                ->atPath('field.options')
                ->setCode(FieldOptions::INVALID_FIELD_OPTIONS_ERROR)
                ->addViolation()
            ;
        }
    }
}
