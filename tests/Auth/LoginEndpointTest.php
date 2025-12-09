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
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
#[Group('endpoints')]
#[Group('auth-endpoints')]
#[Group('auth-login-endpoints')]
final class LoginEndpointTest extends KernelTestCase
{
    #[Group('post-endpoints-success')]
    #[Group('post-auth-login-endpoints-success')]
    public function testPostSuccess(): void
    {
        $this->createUser();

        $this->browser()
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
        ;
    }

    #[TestWith(['test@example.com', 'Wrong-Password2!'])]
    #[TestWith(['bad-Identifier', '$3CR3T'])]
    #[Group('post-endpoints-unauthorized')]
    #[Group('post-auth-login-endpoints-unauthorized')]
    public function testPostUnauthorizedWithBadCredentials(string $identifier, string $plainPassword): void
    {
        $this->createUser();

        $this->browser()
            ->post('/auth/login', [
                'json' => [
                    'identifier' => $identifier,
                    'password' => $plainPassword,
                ],
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
        ;
    }
}
