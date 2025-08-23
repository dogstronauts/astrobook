<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared\ApiPlatform\OpenApi;

/**
 * Utility methods to help generate JSON Schema definitions
 * for OpenAPI documentation. It is designed to be used by OpenAPI factory classes
 * that need to generate schema structures for API documentation.
 */
trait OpenApiFactoryTrait
{
    private function getJsonSchemaFromPathParts(array $pathParts): array
    {
        $jsonSchema = [];

        if (1 === count($pathParts)) {
            $jsonSchema[array_shift($pathParts)] = [
                'type' => 'string',
                'nullable' => false,
            ];

            return $jsonSchema;
        }

        $pathPart = array_shift($pathParts);
        $properties = $this->getJsonSchemaFromPathParts($pathParts);
        $jsonSchema[$pathPart] = [
            'type' => 'object',
            'properties' => $properties,
            'required' => array_keys($properties),
        ];

        return $jsonSchema;
    }
}
