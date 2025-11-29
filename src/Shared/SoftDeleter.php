<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

final class SoftDeleter
{
    private static array $managers = [];

    private readonly \SplObjectStorage $visited;

    public function __construct(private readonly ManagerRegistry $registry)
    {
        $this->visited = new \SplObjectStorage();
    }

    public function markAsDeleted(SoftDeletableInterface $entity, bool $flush = true): void
    {
        if ($this->visited->offsetExists($entity)) {
            return;
        }

        $this->visited->offsetSet($entity);
        $entity->deletedAt = new \DateTimeImmutable();

        $em = $this->getEntityManager($entity);
        $metadata = $em->getClassMetadata($entity::class);

        foreach ($metadata->associationMappings as $association) {
            if (!in_array('remove', $association['cascade'] ?? [], true)) {
                continue;
            }

            $fieldName = $association['fieldName'];
            $related = $metadata->getFieldValue($entity, $fieldName);

            if (is_iterable($related)) {
                foreach ($related as $item) {
                    if ($item instanceof SoftDeletableInterface) {
                        $this->markAsDeleted($item, false);
                    }
                }
            } elseif ($related instanceof SoftDeletableInterface) {
                $this->markAsDeleted($related, false);
            }

            if (ClassMetadata::MANY_TO_ONE === $association['type'] && $association['targetEntity'] === $entity::class) {
                $inverseField = $fieldName;
                $targetEntity = $metadata->getName();

                $children = $this->getEntityManager($entity)
                    ->getRepository($targetEntity)
                    ->findBy([$inverseField => $entity])
                ;

                foreach ($children as $child) {
                    if ($child instanceof SoftDeletableInterface) {
                        $this->markAsDeleted($child, false);
                    }
                }
            }
        }

        if ($flush) {
            $this->flush($entity);
        }
    }

    public function restore(SoftDeletableInterface $entity, bool $flush = true): void
    {
        $entity->deletedAt = null;

        if ($flush) {
            $this->flush($entity);
        }
    }

    public function isDeleted(SoftDeletableInterface $entity): bool
    {
        return null !== $entity->deletedAt;
    }

    private function getObjectManager(SoftDeletableInterface $entity): ObjectManager
    {
        $class = $entity::class;

        if (!isset(self::$managers[$class])) {
            $manager = $this->registry->getManagerForClass($class);
            if (!$manager instanceof ObjectManager) {
                throw new \LogicException(sprintf('none object manager found for « %s » entity', $class));
            }

            self::$managers[$class] = $manager;
        }

        return self::$managers[$class];
    }

    private function getEntityManager(SoftDeletableInterface $entity): EntityManagerInterface
    {
        $manager = $this->getObjectManager($entity);
        if (!$manager instanceof EntityManagerInterface) {
            throw new \LogicException(sprintf('Manager for class « %s » is not an EntityManager', $entity::class));
        }

        return $manager;
    }

    private function flush(SoftDeletableInterface $entity): void
    {
        $this->getObjectManager($entity)->flush();
    }
}
