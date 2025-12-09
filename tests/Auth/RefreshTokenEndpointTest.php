<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Auth;

use Dogstronauts\AstroBook\Tests\Shared\Functional\KernelTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
#[Group('endpoints')]
#[Group('auth-endpoints')]
#[Group('auth-refresh-tokens-endpoints')]
final class RefreshTokenEndpointTest extends KernelTestCase
{
    #[Group('post-endpoints-success')]
    #[Group('post-auth-refresh-tokens-endpoints-success')]
    public function testPostSuccess(): void
    {
        $this->createUser();

        $response = $this->browser()
            ->post('/auth/login', [
                'json' => [
                    'identifier' => 'test@example.com',
                    'password' => '$3CR3T',
                ],
            ])
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertContains('token')
            ->assertContains('refresh_token')
            ->json()
            ->decoded()
        ;

        $this->browser()
            ->post('/auth/refresh_tokens', [
                'json' => [
                    'refresh_token' => $response['refresh_token'],
                ],
            ])
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertContains('token')
            ->assertContains('refresh_token')
        ;
    }

    #[Group('post-endpoints-unauthorized')]
    #[Group('post-auth-refresh-tokens-endpoints-unauthorized')]
    public function testPostUnauthorizedWithInvalidToken(): void
    {
        $this->browser()
            ->post('/auth/refresh_tokens', [
                'json' => [
                    'refresh_token' => 'this-token-does-not-exist',
                ],
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
        ;
    }
}
