<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Functional;

use Dogstronauts\AstroBook\Fixtures\Factory\TaxonomyFactory;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Browser\Json;

/**
 * @internal
 */
#[Group('endpoints')]
#[Group('taxonomy-endpoints')]
final class TaxonomyEndpointTest extends KernelTestCase
{
    #[Group('getCollection-endpoints-success')]
    #[Group('getCollection-taxonomy-endpoints-success')]
    public function testGetCollectionSuccess(): void
    {
        TaxonomyFactory::createOne();

        $this->browser()
            ->get('/taxonomies')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMatches('"@context"', '/contexts/Taxonomy')
            ->assertJsonMatches('"@id"', '/taxonomies')
            ->assertJsonMatches('"@type"', 'Collection')
            ->assertJsonMatches('totalItems', 1)
            ->use(static function (Json $json): void {
                $json->assertMatches('keys("member"[0])', [
                    '@id',
                    '@type',
                    'id',
                    'label',
                    'description',
                ]);
            })
        ;
    }

    #[Group('get-endpoints-success')]
    #[Group('get-taxonomy-endpoints-success')]
    public function testGetSuccess(): void
    {
        $taxonomy = TaxonomyFactory::createOne([
            'label' => 'Test Taxonomy',
            'description' => 'This is a test taxonomy',
        ]);
        $taxonomyIri = $this->getIriFromResource($taxonomy);

        $this->browser()
            ->get($taxonomyIri)
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMatches('"@context"', '/contexts/Taxonomy')
            ->assertJsonMatches('"@id"', $taxonomyIri)
            ->assertJsonMatches('"@type"', 'Taxonomy')
            ->assertJsonMatches('label', 'Test Taxonomy')
            ->assertJsonMatches('description', 'This is a test taxonomy')
        ;
    }

    #[Group('post-endpoints-success')]
    #[Group('post-taxonomy-endpoints-success')]
    public function testPostSuccess(): void
    {
        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->post('/taxonomies', [
                'json' => [
                    'label' => $label = 'Test Taxonomy',
                    'description' => $description = 'This is a test taxonomy',
                ],
            ])
            ->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertContains('id')
            ->assertJsonMatches('label', $label)
            ->assertJsonMatches('description', $description)
        ;
    }

    #[Group('post-endpoints-success')]
    #[Group('post-taxonomy-endpoints-success')]
    public function testPostWithParentSuccess(): void
    {
        $parent = TaxonomyFactory::createOne(['label' => 'Parent Taxonomy']);
        $parentIri = $this->getIriFromResource($parent);

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->post('/taxonomies', [
                'json' => [
                    'label' => $label = 'Child Taxonomy',
                    'description' => $description = 'This is a child taxonomy',
                    'parent' => $parentIri,
                ],
            ])
            ->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertContains('id')
            ->assertJsonMatches('label', $label)
            ->assertJsonMatches('description', $description)
            ->assertJsonMatches('parent', $parentIri)
        ;
    }

    #[Group('patch-endpoints-success')]
    #[Group('patch-taxonomy-endpoints-success')]
    public function testPatchSuccess(): void
    {
        $taxonomy = TaxonomyFactory::createOne(['label' => 'Old Label']);
        $taxonomyIri = $this->getIriFromResource($taxonomy);

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->patch($taxonomyIri, [
                'json' => [
                    'label' => $label = 'Updated Taxonomy',
                    'description' => $description = 'This is an updated taxonomy',
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ],
            ])
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertContains('id')
            ->assertJsonMatches('label', $label)
            ->assertJsonMatches('description', $description)
        ;
    }

    #[Group('delete-endpoints-success')]
    #[Group('delete-taxonomy-endpoints-success')]
    public function testDeleteSuccess(): void
    {
        $taxonomyIri = $this->getIriFromResource(TaxonomyFactory::createOne());

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->delete($taxonomyIri)
            ->assertStatus(Response::HTTP_NO_CONTENT)
        ;
    }

    #[Group('post-endpoints-forbidden')]
    #[Group('post-taxonomy-endpoints-forbidden')]
    public function testPostForbidden(): void
    {
        $this->browser()
            ->actingAs($this->createUser())
            ->post('/taxonomies', [
                'json' => [],
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN)
        ;
    }

    #[Group('patch-endpoints-forbidden')]
    #[Group('patch-taxonomy-endpoints-forbidden')]
    public function testPatchForbidden(): void
    {
        $taxonomyIri = $this->getIriFromResource(TaxonomyFactory::createOne());

        $this->browser()
            ->actingAs($this->createUser())
            ->patch($taxonomyIri, [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN)
        ;
    }

    #[Group('delete-endpoints-forbidden')]
    #[Group('delete-taxonomy-endpoints-forbidden')]
    public function testDeleteForbidden(): void
    {
        $taxonomyIri = $this->getIriFromResource(TaxonomyFactory::createOne());

        $this->browser()
            ->actingAs($this->createUser())
            ->delete($taxonomyIri)
            ->assertStatus(Response::HTTP_FORBIDDEN)
        ;
    }
}
