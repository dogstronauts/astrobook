<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Auth\ApiPlatform\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\MediaType;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use Dogstronauts\AstroBook\Shared\ApiPlatform\OpenApi\OpenApiFactoryTrait;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Replacement for Lexik JWT Authentication Bundle OpenAPI factory to avoid
 * deprecated ArrayObject usage (object as backing array).
 *
 * We keep the same behavior while ensuring we only pass PHP arrays to ArrayObject.
 */
#[AsDecorator('api_platform.openapi.factory')]
#[AsAlias('lexik_jwt_authentication.api_platform.openapi.factory', public: false)]
final readonly class LexikOpenApiFactory implements OpenApiFactoryInterface
{
    use OpenApiFactoryTrait;

    public function __construct(
        private OpenApiFactoryInterface $decorated,
        private string $checkPath = '/auth/login',
        private string $usernamePath = 'identifier',
        private string $passwordPath = 'password',
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        // Security scheme
        $openApi->getComponents()->getSecuritySchemes()->offsetSet(
            'JWT',
            new \ArrayObject([
                'type' => 'http',
                'scheme' => 'bearer',
                'bearerFormat' => 'JWT',
            ])
        );

        $openApi->getPaths()->addPath($this->checkPath, new PathItem()->withPost(
            new Operation()
                ->withOperationId('api_auth_post')
                ->withTags(['Auth'])
                ->withResponses([
                    Response::HTTP_OK => [
                        'description' => 'User token created',
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
                                    ],
                                    'required' => ['token'],
                                ],
                            ],
                        ],
                    ],
                ])
                ->withSummary('Creates a user token.')
                ->withDescription('Creates a user token.')
                ->withRequestBody(
                    new RequestBody()
                        ->withDescription('The login data')
                        ->withContent(new \ArrayObject([
                            'application/json' => new MediaType(new \ArrayObject([
                                'type' => 'object',
                                'properties' => $properties = array_merge_recursive(
                                    $this->getJsonSchemaFromPathParts(explode('.', $this->usernamePath)),
                                    $this->getJsonSchemaFromPathParts(explode('.', $this->passwordPath))
                                ),
                                'required' => array_keys($properties),
                            ])),
                        ]))
                        ->withRequired(true)
                )
        ));

        return $openApi;
    }
}
