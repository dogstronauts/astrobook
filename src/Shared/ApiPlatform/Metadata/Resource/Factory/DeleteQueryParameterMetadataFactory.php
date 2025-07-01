<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared\ApiPlatform\Metadata\Resource\Factory;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use Dogstronauts\AstroBook\Shared\Taxonomies\Model\SoftDeletableInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator('api_platform.metadata.resource.metadata_collection_factory.parameter')]
readonly class DeleteQueryParameterMetadataFactory implements ResourceMetadataCollectionFactoryInterface
{
    public function __construct(private ResourceMetadataCollectionFactoryInterface $decorated)
    {
    }

    public function create(string $resourceClass): ResourceMetadataCollection
    {
        $resourceMetadataCollection = $this->decorated->create($resourceClass);

        if (!is_a($resourceClass, SoftDeletableInterface::class, true)) {
            return $resourceMetadataCollection;
        }

        foreach ($resourceMetadataCollection as $resourceIndex => $apiResource) {
            if (!$apiResource instanceof ApiResource) {
                continue;
            }

            $updatedOperations = [];

            foreach ($apiResource->getOperations() as $name => $operation) {
                if (!($operation instanceof GetCollection || $operation instanceof Get)) {
                    $updatedOperations[$name] = $operation;
                    continue;
                }

                $parameters = $operation->getParameters() ?? [];

                $parameters[] = new QueryParameter(
                    key: 'deleted',
                    schema: ['type' => 'boolean'],
                    description: 'Filter items based on their deleted status (true or false)',
                    required: false
                );

                $updatedOperations[$name] = $operation->withParameters($parameters);
            }

            $resourceMetadataCollection[$resourceIndex] = $apiResource->withOperations(new Operations($updatedOperations));
        }

        return $resourceMetadataCollection;
    }
}
