<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Functional;

use Dogstronauts\AstroBook\Fixtures\Factory\UserFactory;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Browser\Json;

/**
 * @internal
 */
#[Group('endpoints')]
#[Group('users-endpoints')]
final class UserEndpointTest extends KernelTestCase
{
    #[Group('getCollection-endpoints-success')]
    #[Group('getCollection-users-endpoints-success')]
    public function testGetCollectionSuccess(): void
    {
        UserFactory::createMany(3);

        $this->browser()
            ->actingAs($this->createUser())
            ->get('/users')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMatches('"@context"', '/contexts/User')
            ->assertJsonMatches('"@id"', '/users')
            ->assertJsonMatches('"@type"', 'Collection')
            ->assertJsonMatches('totalItems', 4)
            ->use(static function (Json $json): void {
                $json->assertMatches('keys("member"[0])', [
                    '@id',
                    '@type',
                    'id',
                    'identifier',
                    'roles',
                ]);
            })
        ;
    }

    #[Group('get-endpoints-success')]
    #[Group('get-users-endpoints-success')]
    public function testGetSuccess(): void
    {
        $user = $this->createUser();

        $userIri = $this->getIriFromResource($user);

        $this->browser()
            ->actingAs($user)
            ->get($userIri)
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertContains('id')
            ->assertContains('identifier')
            ->assertContains('roles')
            ->assertNotContains('password')
        ;
    }

    #[Group('post-endpoints-success')]
    #[Group('post-users-endpoints-success')]
    public function testPostSuccess(): void
    {
        $this->browser()
            ->actingAs($this->createUser())
            ->post('/users', [
                'json' => [
                    'identifier' => 'john@doe',
                    'plainPassword' => '4IyjFA/4Hc1C4xK',
                    'roles' => ['ROLE_TEST'],
                ],
            ])
            ->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertContains('id')
            ->assertJsonMatches('identifier', 'john@doe')
            ->assertJsonMatches('roles', ['ROLE_TEST'])
            ->assertNotContains('password')
        ;
    }

    #[Group('patch-endpoints-success')]
    #[Group('patch-users-endpoints-success')]
    public function testPatchSuccess(): void
    {
        $user = $this->createUser();

        $userIri = $this->getIriFromResource($user);

        $this->browser()
            ->actingAs($user)
            ->patch($userIri, [
                'json' => [
                    'identifier' => 'test_updated@example.com',
                    'plainPassword' => '4IyjFA/4Hc1C4xK',
                    'roles' => ['ROLE_TEST_UPDATED'],
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ],
            ])
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertContains('id')
            ->assertJsonMatches('identifier', 'test_updated@example.com')
            ->assertJsonMatches('roles', ['ROLE_TEST_UPDATED'])
            ->assertNotContains('password')
        ;
    }

    #[Group('delete-endpoints-success')]
    #[Group('delete-users-endpoints-success')]
    public function testDeleteSuccess(): void
    {
        $user = $this->createUser();

        $userIri = $this->getIriFromResource($user);

        $this->browser()
            ->actingAs($user)
            ->delete($userIri)
            ->assertStatus(Response::HTTP_NO_CONTENT)
        ;
    }
}
