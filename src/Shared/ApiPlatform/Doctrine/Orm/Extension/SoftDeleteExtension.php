<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared\ApiPlatform\Doctrine\Orm\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Dogstronauts\AstroBook\Shared\SoftDeletableInterface;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class SoftDeleteExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private const string FILTER_PARAM = 'deleted';

    private const string ROLE_ALLOWED = 'ROLE_PLATFORM';

    public function __construct(private Security $security)
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $this->applyFilter($queryBuilder, $resourceClass, $context);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $this->applyFilter($queryBuilder, $resourceClass, $context);
    }

    private function applyFilter(QueryBuilder $queryBuilder, string $resourceClass, array $context): void
    {
        if (!is_a($resourceClass, SoftDeletableInterface::class, true)) {
            return;
        }

        if ($this->shouldExcludeDeleted($context)) {
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.deletedAt IS NULL', $alias));
        }
    }

    /**
     * Evaluates whether soft-deleted entities must be excluded.
     */
    private function shouldExcludeDeleted(array $context): bool
    {
        return !(
            filter_var($context['filters'][self::FILTER_PARAM] ?? false, \FILTER_VALIDATE_BOOLEAN)
            && $this->security->isGranted(self::ROLE_ALLOWED)
        );
    }
}
