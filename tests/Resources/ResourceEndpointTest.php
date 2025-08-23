<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Resources;

use Dogstronauts\AstroBook\Fixtures\Factory\ResourceFactory;
use Dogstronauts\AstroBook\Fixtures\Factory\ResourceTypeFactory;
use Dogstronauts\AstroBook\Tests\Shared\Functional\KernelTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Browser\Json;

/**
 * @internal
 */
#[Group('endpoints')]
#[Group('resources-endpoints')]
final class ResourceEndpointTest extends KernelTestCase
{
    #[Group('getCollection-endpoints-success')]
    #[Group('getCollection-resources-endpoints-success')]
    public function testGetCollectionSuccess(): void
    {
        ResourceFactory::createMany(1);

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->get('/resources')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMatches('"@context"', '/contexts/Resource')
            ->assertJsonMatches('"@id"', '/resources')
            ->assertJsonMatches('"@type"', 'Collection')
            ->assertJsonMatches('totalItems', 1)
            ->use(static function (Json $json): void {
                $json->assertMatches('keys("member"[0])', [
                    '@id',
                    '@type',
                    'id',
                    'type',
                    'label',
                    'description',
                ]);
            })
        ;
    }

    #[Group('get-endpoints-success')]
    #[Group('get-resources-endpoints-success')]
    public function testGetSuccess(): void
    {
        $resource = ResourceFactory::createOne();

        $resourceIri = $this->getIriFromResource($resource);

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->get($resourceIri)
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertContains('id')
            ->assertContains('label')
            ->assertContains('description')
        ;
    }

    #[Group('post-endpoints-success')]
    #[Group('post-resources-endpoints-success')]
    public function testPostSuccess(): void
    {
        $resourceType = ResourceTypeFactory::createOne();

        $resourceTypeIri = $this->getIriFromResource($resourceType);

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->post('/resources', [
                'json' => [
                    'type' => $resourceTypeIri,
                    'label' => $label = 'label',
                    'description' => $description = 'description',
                ],
            ])
            ->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertContains('id')
            ->assertJsonMatches('type', $resourceTypeIri)
            ->assertJsonMatches('label', $label)
            ->assertJsonMatches('description', $description)
        ;
    }

    #[Group('patch-endpoints-success')]
    #[Group('patch-resources-endpoints-success')]
    public function testPatchSuccess(): void
    {
        $resource = ResourceFactory::createOne();

        $resourceIri = $this->getIriFromResource($resource);

        $resourceType = ResourceTypeFactory::createOne();

        $resourceTypeIri = $this->getIriFromResource($resourceType);

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->patch($resourceIri, [
                'json' => [
                    'type' => $resourceTypeIri,
                    'label' => $label = 'updated label',
                    'description' => $description = 'updated description',
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ],
            ])
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertContains('id')
            ->assertJsonMatches('type', $resourceTypeIri)
            ->assertJsonMatches('label', $label)
            ->assertJsonMatches('description', $description)
        ;
    }

    #[Group('delete-endpoints-success')]
    #[Group('delete-resources-endpoints-success')]
    public function testDeleteSuccess(): void
    {
        $resource = ResourceFactory::createOne();

        $resourceIri = $this->getIriFromResource($resource);

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->delete($resourceIri)
            ->assertStatus(Response::HTTP_NO_CONTENT)
        ;
    }
}
