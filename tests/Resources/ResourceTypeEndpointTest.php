<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Resources;

use Dogstronauts\AstroBook\Fixtures\Factory\ResourceTypeFactory;
use Dogstronauts\AstroBook\Resources\Model\FieldType;
use Dogstronauts\AstroBook\Tests\Shared\Functional\KernelTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Browser\Json;

/**
 * @internal
 */
#[Group('endpoints')]
#[Group('resource-types-endpoints')]
final class ResourceTypeEndpointTest extends KernelTestCase
{
    #[Group('getCollection-endpoints-success')]
    #[Group('getCollection-resource-types-endpoints-success')]
    public function testGetCollectionSuccess(): void
    {
        ResourceTypeFactory::createMany(1);

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->get('/resource_types')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMatches('"@context"', '/contexts/ResourceType')
            ->assertJsonMatches('"@id"', '/resource_types')
            ->assertJsonMatches('"@type"', 'Collection')
            ->assertJsonMatches('totalItems', 1)
            ->use(static function (Json $json): void {
                $json->assertMatches('keys("member"[0])', [
                    '@id',
                    '@type',
                    'id',
                    'label',
                    'description',
                    'fields',
                ]);
            })
        ;
    }

    #[Group('get-endpoints-success')]
    #[Group('get-resource-types-endpoints-success')]
    public function testGetSuccess(): void
    {
        $resourceType = ResourceTypeFactory::createOne();

        $resourceTypeIri = $this->getIriFromResource($resourceType);

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->get($resourceTypeIri)
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertContains('id')
            ->assertContains('label')
            ->assertContains('description')
            ->assertContains('fields')
        ;
    }

    #[Group('post-endpoints-success')]
    #[Group('post-resource-types-endpoints-success')]
    public function testPostSuccess(): void
    {
        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->post('/resource_types', [
                'json' => [
                    'label' => 'Test Resource Type',
                    'description' => 'Description du type de ressource',
                    'fields' => [
                        ['type' => FieldType::STRING->value, 'label' => 'field 1'],
                    ],
                ],
            ])
            ->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertContains('id')
            ->assertJsonMatches('label', 'Test Resource Type')
            ->assertJsonMatches('description', 'Description du type de ressource')
        ;
    }

    #[Group('patch-endpoints-success')]
    #[Group('patch-resource-types-endpoints-success')]
    public function testPatchSuccess(): void
    {
        $resourceType = ResourceTypeFactory::createOne();

        $resourceTypeIri = $this->getIriFromResource($resourceType);

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->patch($resourceTypeIri, [
                'json' => [
                    'label' => 'Updated Resource Type',
                    'description' => 'Description mise à jour',
                    'fields' => [
                        [
                            'type' => FieldType::STRING->value,
                            'label' => 'Champ 1',
                        ],
                        [
                            'type' => FieldType::INT->value,
                            'label' => 'Champ 2',
                        ],
                        [
                            'type' => FieldType::BOOL->value,
                            'label' => 'Champ 3',
                        ],
                    ],
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ],
            ])
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertContains('id')
            ->assertJsonMatches('label', 'Updated Resource Type')
            ->assertJsonMatches('description', 'Description mise à jour')
        ;
    }

    #[Group('delete-endpoints-success')]
    #[Group('delete-resource-types-endpoints-success')]
    public function testDeleteSuccess(): void
    {
        $resourceType = ResourceTypeFactory::createOne();

        $resourceTypeIri = $this->getIriFromResource($resourceType);

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->delete($resourceTypeIri)
            ->assertStatus(Response::HTTP_NO_CONTENT)
        ;
    }
}
