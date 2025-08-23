<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared\ApiPlatform\Doctrine\Orm\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use Doctrine\ORM\QueryBuilder;

final class DiscriminatorFilter extends AbstractFilter
{
    public const string PARAMETER_DISCRIMINATOR = 'discriminator';

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if (self::PARAMETER_DISCRIMINATOR !== $property) {
            return;
        }

        $values = $this->normalizeValues((array) $value);

        if ([] === $values) {
            return;
        }

        $metadata = $this->managerRegistry->getManager()->getClassMetadata($resourceClass);

        $discriminatorMap = $metadata->discriminatorMap ?? [];

        if ([] === $discriminatorMap) {
            return;
        }

        $discriminators = array_keys($discriminatorMap);

        $validDiscriminatorValues = array_filter(
            $values,
            static fn (string $fromRequest): bool => in_array($fromRequest, $discriminators, true)
        );

        if ([] === $validDiscriminatorValues) {
            return;
        }

        $orX = $queryBuilder->expr()->orX();
        $alias = $queryBuilder->getRootAliases()[0];

        foreach ($validDiscriminatorValues as $validDiscriminatorValue) {
            $orX->add(
                $queryBuilder->expr()->isInstanceOf($alias, $discriminatorMap[$validDiscriminatorValue])
            );
        }

        $queryBuilder->andWhere($orX);
    }

    /**
     * @return array{search: array}
     */
    public function getDescription(string $resourceClass): array
    {
        return [
            sprintf('%s[]', self::PARAMETER_DISCRIMINATOR) => [
                'property' => self::PARAMETER_DISCRIMINATOR,
                'type' => 'string',
                'required' => false,
                'is_collection' => true,
                'openapi' => new Parameter(
                    name: self::PARAMETER_DISCRIMINATOR,
                    in: 'query',
                    description: 'Type of the Doctrine entity in the Inheritance Map (discriminator)',
                    allowEmptyValue: true,
                    explode: false,
                    allowReserved: false,
                    example: 'Custom example that will be in the documentation and be the default value of the sandbox',
                ),
            ],
        ];
    }

    protected function normalizeValues(array $values): ?array
    {
        foreach ($values as $key => $value) {
            if (!\is_int($key) || !\is_string($value)) {
                unset($values[$key]);
            }
        }

        return array_values($values);
    }
}
