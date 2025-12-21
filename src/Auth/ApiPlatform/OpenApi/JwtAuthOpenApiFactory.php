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
use ApiPlatform\OpenApi\Model\Response as OpenApiResponse;
use ApiPlatform\OpenApi\Model\SecurityScheme;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpFoundation\Response;

#[AsDecorator('lexik_jwt_authentication.api_platform.openapi.factory')]
final readonly class JwtAuthOpenApiFactory implements OpenApiFactoryInterface
{
    private const string AUTH_PATH = '/auth';

    private const string REFRESH_PATH = '/auth/refresh_tokens';

    private const string SECURITY_SCHEME = 'BearerAuth';

    public function __construct(
        private OpenApiFactoryInterface $decorated,
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        foreach ($this->transformers() as $transformer) {
            $openApi = $transformer($openApi);
        }

        return $openApi;
    }

    /**
     * @return iterable<callable(OpenApi): OpenApi>
     */
    private function transformers(): iterable
    {
        yield $this->removeLexikSecurityScheme(...);
        yield $this->addBearerAuthenticationSecurityScheme(...);
        yield $this->addAuthEndpoint(...);
        yield $this->addRefreshTokenEndpoint(...);
    }

    private function removeLexikSecurityScheme(OpenApi $openApi): OpenApi
    {
        $openApi->getComponents()
            ->getSecuritySchemes()
            ?->offsetUnset('JWT')
        ;

        return $openApi;
    }

    private function addBearerAuthenticationSecurityScheme(OpenApi $openApi): OpenApi
    {
        $scheme = new SecurityScheme(
            type: 'http',
            description: 'JWT Bearer authentication',
            scheme: 'bearer',
            bearerFormat: 'JWT'
        );

        $components = $openApi->getComponents()->withSecuritySchemes(
            new \ArrayObject([self::SECURITY_SCHEME => $scheme])
        );

        return $openApi
            ->withComponents($components)
            ->withSecurity([[self::SECURITY_SCHEME => []]])
        ;
    }

    private function addAuthEndpoint(OpenApi $openApi): OpenApi
    {
        return $this->addPostEndpoint(
            openApi: $openApi,
            path: self::AUTH_PATH,
            operation: new Operation()
                ->withOperationId('api_auth_post')
                ->withTags(['Auth'])
                ->withSummary('Authenticate user')
                ->withDescription('Authenticates user credentials and returns a JWT access token and refresh token.')
                ->withSecurity([])
                ->withRequestBody(new RequestBody()
                    ->withRequired(true)
                    ->withDescription('User credentials')
                    ->withContent(new \ArrayObject([
                        'application/json' => new MediaType()
                            ->withSchema(new \ArrayObject(['type' => 'object', 'properties' => [
                                'identifier' => [
                                    'type' => 'string',
                                    'description' => 'User identifier',
                                    'writeOnly' => true,
                                    'format' => 'email',
                                    'minLength' => 1,
                                    'maxLength' => 128,
                                ],
                                'password' => [
                                    'type' => 'string',
                                    'description' => 'User password',
                                    'writeOnly' => true,
                                    'format' => 'password',
                                    'minLength' => 8,
                                    'maxLength' => 128,
                                ],
                            ]]))
                            ->withExamples(new \ArrayObject(['auth-example' => [
                                'summary' => 'Example of authentication request',
                                'value' => ['identifier' => 'demo@demo.fr', 'password' => 'demo1234%'],
                            ]])),
                    ])))
                ->withResponses([Response::HTTP_OK => $this->buildAuthHttpOkResponse()]),
        );
    }

    private function addRefreshTokenEndpoint(OpenApi $openApi): OpenApi
    {
        return $this->addPostEndpoint(
            openApi: $openApi,
            path: self::REFRESH_PATH,
            operation: new Operation()
                ->withOperationId('api_auth_refresh_tokens_post')
                ->withTags(['Auth'])
                ->withSummary('Refresh JWT token')
                ->withDescription('Exchanges a valid refresh token for a new JWT access token and refresh token.')
                ->withSecurity([])
                ->withRequestBody(
                    new RequestBody()
                        ->withRequired(true)
                        ->withDescription('Refresh token used to obtain a new JWT access token')
                        ->withContent(new \ArrayObject([
                            'application/json' => new MediaType()
                                ->withSchema(new \ArrayObject([
                                    'type' => 'object',
                                    'required' => ['refresh_token'],
                                    'properties' => [
                                        'refresh_token' => [
                                            'type' => 'string',
                                            'description' => 'Valid refresh token',
                                        ],
                                    ],
                                ]))
                                ->withExamples(new \ArrayObject(['auth-example' => [
                                    'summary' => 'Example of refresh token request',
                                    'value' => ['refresh_token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...'],
                                ]])),
                        ]))
                )
                ->withResponses([Response::HTTP_OK => $this->buildAuthHttpOkResponse()])
        );
    }

    private function addPostEndpoint(OpenApi $openApi, string $path, Operation $operation): OpenApi
    {
        $openApi->getPaths()->addPath($path, ($openApi->getPaths()->getPath($path) ?? new PathItem())->withPost($operation));

        return $openApi;
    }

    private function buildAuthHttpOkResponse(): OpenApiResponse
    {
        return new OpenApiResponse()
            ->withDescription('Returns a JWT access token and a refresh token for the authenticated user.')
            ->withContent(new \ArrayObject([
                'application/json' => new MediaType()
                    ->withSchema(new \ArrayObject([
                        'type' => 'object',
                        'required' => ['token', 'refresh_token'],
                        'properties' => [
                            'token' => [
                                'type' => 'string',
                                'readOnly' => true,
                                'description' => 'JWT access token',
                            ],
                            'refresh_token' => [
                                'type' => 'string',
                                'readOnly' => true,
                                'description' => 'Refresh token',
                            ],
                        ],
                    ]))
                    ->withExamples(new \ArrayObject(['auth-example' => [
                        'summary' => 'Example of authentication response',
                        'value' => ['token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...', 'refresh_token' => 'def50200a1b2c3d4e5f6...'],
                    ]])),
            ]))
        ;
    }
}
