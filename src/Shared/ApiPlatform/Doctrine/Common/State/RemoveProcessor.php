<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared\ApiPlatform\Doctrine\Common\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Dogstronauts\AstroBook\Shared\SoftDeleter;
use Dogstronauts\AstroBook\Shared\Taxonomies\Model\SoftDeletableInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator('api_platform.doctrine.orm.state.remove_processor')]
final class RemoveProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SoftDeleter $softDeleter
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof SoftDeletableInterface) {
            $this->softDeleter->markAsDeleted($data);

            return;
        }

        $this->entityManager->remove($data);

        $this->entityManager->flush();
    }
}
