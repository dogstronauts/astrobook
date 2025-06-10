<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Functional;

use Dogstronauts\AstroBook\Fixtures\Factory\EventFactory;
use Dogstronauts\AstroBook\Fixtures\Factory\UserFactory;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Browser\Json;

/**
 * @internal
 */
#[Group('endpoints')]
#[Group('events-endpoints')]
final class EventEndpointTest extends KernelTestCase
{
    #[Group('getCollection-endpoints-success')]
    #[Group('getCollection-events-endpoints-success')]
    public function testGetCollectionSuccess(): void
    {
        EventFactory::createMany(3);

        $this->browser()
            ->get('/events')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMatches('"@context"', '/contexts/Event')
            ->assertJsonMatches('"@id"', '/events')
            ->assertJsonMatches('"@type"', 'Collection')
            ->assertJsonMatches('totalItems', 3)
            ->use(static function (Json $json) {
                foreach (['@id', '@type', 'id', 'label', 'startAt', 'endAt', 'status', 'duration'] as $key) {
                    $json->assertHas('member[0].' . $key);
                }
            })
        ;
    }

    #[Group('get-endpoints-success')]
    #[Group('get-events-endpoints-success')]
    public function testGetSuccess(): void
    {
        $event = EventFactory::createOne();
        $iri = $this->getIriFromResource($event);

        $this->browser()
            ->get($iri)
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertContains('id')
            ->assertContains('label')
            ->assertContains('startAt')
            ->assertContains('endAt')
            ->assertContains('status')
            ->assertContains('duration')
        ;
    }

    #[Group('post-endpoints-success')]
    #[Group('post-events-endpoints-success')]
    public function testPostSuccess(): void
    {
        $user = UserFactory::createOne(['roles' => ['ROLE_PLATFORM']]);

        $payload = [
            'label' => 'Test Event',
            'description' => 'An event description',
            'startAt' => '2025-07-01T10:00:00+02:00',
            'endAt' => '2025-07-01T12:30:00+02:00',
            'status' => 'draft',
            'duration' => 150,
        ];

        $this->browser()
            ->actingAs($user)
            ->post('/events', ['json' => $payload])
            ->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonMatches('label', 'Test Event')
            ->assertJsonMatches('description', 'An event description')
            ->assertJsonMatches('status', 'draft')
            ->assertJsonMatches('duration', 150)
        ;
    }

    #[Group('post-endpoints-forbidden')]
    #[Group('post-events-endpoints-forbidden')]
    public function testPostForbidden(): void
    {
        $this->browser()
            ->actingAs(UserFactory::createOne())
            ->post('/events', ['json' => [
                'label' => 'Hack Event',
                'startAt' => '2025-07-01T10:00:00+02:00',
                'endAt' => '2025-07-01T11:00:00+02:00',
                'duration' => 60,
            ]])
            ->assertStatus(Response::HTTP_FORBIDDEN)
        ;
    }

    #[Group('patch-endpoints-success')]
    #[Group('patch-events-endpoints-success')]
    public function testPatchSuccess(): void
    {
        $user = UserFactory::createOne(['roles' => ['ROLE_PLATFORM']]);
        $event = EventFactory::createOne(['label' => 'Old Label']);
        $iri = $this->getIriFromResource($event);

        $this->browser()
            ->actingAs($user)
            ->patch($iri, [
                'json' => ['label' => 'New Label'],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ])
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMatches('label', 'New Label')
        ;
    }

    #[Group('patch-endpoints-forbidden')]
    #[Group('patch-events-endpoints-forbidden')]
    public function testPatchForbidden(): void
    {
        $event = EventFactory::createOne();
        $iri = $this->getIriFromResource($event);

        $this->browser()
            ->actingAs(UserFactory::createOne())
            ->patch($iri, [
                'json' => ['label' => 'Attempted'],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN)
        ;
    }

    #[Group('delete-endpoints-success')]
    #[Group('delete-events-endpoints-success')]
    public function testDeleteSuccess(): void
    {
        $user = UserFactory::createOne(['roles' => ['ROLE_PLATFORM']]);
        $event = EventFactory::createOne();
        $iri = $this->getIriFromResource($event);

        $this->browser()
            ->actingAs($user)
            ->delete($iri)
            ->assertStatus(Response::HTTP_NO_CONTENT)
        ;
    }

    #[Group('delete-endpoints-forbidden')]
    #[Group('delete-events-endpoints-forbidden')]
    public function testDeleteForbidden(): void
    {
        $event = EventFactory::createOne();
        $iri = $this->getIriFromResource($event);

        $this->browser()
            ->actingAs(UserFactory::createOne())
            ->delete($iri)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
