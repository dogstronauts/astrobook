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
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;

/**
 * Decorates Lexik JWT Authentication's OpenAPI factory to document
 * the authentication endpoints in the OpenAPI/Swagger specification.
 *
 * - Adds documentation for the authentication endpoint (configurable via checkPath parameter)
 * - Documents the expected request format:
 *   - Identifier field (configurable via usernamePath parameter)
 *   - Password field (configurable via passwordPath parameter)
 * - Documents the successful response format:
 *   - JWT token (string)
 *   - Refresh token (string)
 * - Includes response codes and descriptions
 * - Tags the endpoint under "Security" category
 *
 * The endpoint configuration is fully customizable through config/packages/lexik_jwt_authentication.yaml:
 * - lexik_jwt_authentication.api_platform.path: Defines the authentication endpoint path
 * - lexik_jwt_authentication.api_platform.username: Defines the username field name
 * - lexik_jwt_authentication.api_platform.password: Defines the password field name
 */
#[AsDecorator('lexik_jwt_authentication.api_platform.openapi.factory')]
final readonly class LexitOpenApiFactory implements OpenApiFactoryInterface
{
    use OpenApiFactoryTrait;

    public function __construct(
        private OpenApiFactoryInterface $decorated,
        #[Autowire(param: 'lexik_jwt_authentication.api_platform.path')]
        private string $checkPath,
        #[Autowire(param: 'lexik_jwt_authentication.api_platform.username')]
        private string $usernamePath,
        #[Autowire(param: 'lexik_jwt_authentication.api_platform.password')]
        private string $passwordPath
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $openApi->getPaths()->addPath(sprintf('/%s', $this->checkPath), new PathItem()->withPost(
            new Operation()
                ->withOperationId('authenticate_post')
                ->withTags(['Security'])
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
                ->withSummary('Creates a user token.')
                ->withDescription('Creates a user token.')
                ->withRequestBody(
                    new RequestBody()
                        ->withDescription('The identifiers to authenticate')
                        ->withContent(new \ArrayObject([
                            'application/json' => new MediaType(new \ArrayObject(new \ArrayObject([
                                'type' => 'object',
                                'properties' => $properties = array_merge_recursive($this->getJsonSchemaFromPathParts(explode('.', $this->usernamePath)), $this->getJsonSchemaFromPathParts(explode('.', $this->passwordPath))),
                                'required' => array_keys($properties),
                            ]))),
                        ]))
                        ->withRequired(true)
                )
        ));

        return $openApi;
    }
}
