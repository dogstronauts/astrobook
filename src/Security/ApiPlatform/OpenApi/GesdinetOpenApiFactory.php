<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Security\ApiPlatform\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\MediaType;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use Dogstronauts\AstroBook\ApiPlatform\OpenApi\OpenApiFactoryTrait;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Decorates the API Platform's OpenApiFactory to add JWT refresh token
 * endpoint documentation to the OpenAPI specification. It adds a dedicated path
 * for token refresh operations with appropriate request/response schemas.
 *
 * The factory implements the OpenApiFactoryInterface and uses the decorator pattern
 * to extend the core API Platform functionality without modifying it directly.
 *
 * It exposes a POST endpoint at /refresh_tokens that accepts a refresh token and
 * returns a new JWT token along with a new refresh token. This documentation helps
 * API consumers understand how to refresh authentication tokens when they expire.
 */
#[AsDecorator('api_platform.openapi.factory')]
#[AsAlias('gesdinet_jwt_refresh.api_platform.openapi.factory', public: false)]
final readonly class GesdinetOpenApiFactory implements OpenApiFactoryInterface
{
    use OpenApiFactoryTrait;

    public function __construct(
        private OpenApiFactoryInterface $decorated,
        private ?string $refreshTokenPath = null
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $openApi->getPaths()->addPath('/refresh_tokens', new PathItem()->withPost(
            new Operation()
                ->withOperationId('api_refresh_tokens_post')
                ->withTags(['Security'])
                ->withResponses([
                    Response::HTTP_OK => [
                        'description' => 'User token refreshed.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'token' => [
                                            'readOnly' => true,
                                            'type' => 'string',
                                            'nullable' => false,
                                        ],
                                        'refresh_token' => [
                                            'readOnly' => true,
                                            'type' => 'string',
                                            'nullable' => false,
                                        ],
                                    ],
                                    'required' => ['token', 'refresh_token'],
                                ],
                            ],
                        ],
                    ],
                ])
                ->withSummary('Refresh a user token.')
                ->withDescription('Refresh a user token.')
                ->withRequestBody(
                    new RequestBody()
                        ->withDescription('The refresh_token to refresh token')
                        ->withContent(new \ArrayObject([
                            'application/json' => new MediaType(new \ArrayObject(new \ArrayObject([
                                'type' => 'object',
                                'properties' => $properties = $this->getJsonSchemaFromPathParts(explode('.', 'refresh_token')),
                                'required' => array_keys($properties),
                            ]))),
                        ]))
                        ->withRequired(true)
                )
        ));

        return $openApi;
    }
}
