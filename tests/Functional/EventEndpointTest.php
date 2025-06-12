<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Functional;

use Dogstronauts\AstroBook\Events\Model\EventStatus;
use Dogstronauts\AstroBook\Fixtures\Factory\EventFactory;
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
            ->use(static function (Json $json): void {
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
        $eventIri = $this->getIriFromResource($event);

        $this->browser()
            ->get($eventIri)
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
        $startAt = new \DateTimeImmutable();

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->post('/events', [
                'json' => [
                    'status' => $status = EventStatus::DRAFT->value,
                    'label' => $label = 'Test Event',
                    'description' => $description = 'An event description',
                    'startAt' => $startAt->format(\DateTimeInterface::ATOM),
                    'duration' => $duration = 150,
                ],
            ])
            ->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonMatches('status', $status)
            ->assertJsonMatches('label', $label)
            ->assertJsonMatches('description', $description)
            ->assertJsonMatches('duration', $duration)
            ->assertJsonMatches('startAt', $startAt->format(\DateTimeInterface::ATOM))
            ->assertJsonMatches('endAt', $startAt->modify(sprintf('+%d minutes', $duration))->format(\DateTimeInterface::ATOM))
        ;
    }

    #[Group('post-endpoints-forbidden')]
    #[Group('post-events-endpoints-forbidden')]
    public function testPostForbidden(): void
    {
        $this->browser()
            ->actingAs($this->createUser())
            ->post('/events', [
                'json' => [],
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN)
        ;
    }

    #[Group('patch-endpoints-success')]
    #[Group('patch-events-endpoints-success')]
    public function testPatchSuccess(): void
    {
        $event = EventFactory::createOne(['label' => 'Old Label']);
        $eventIri = $this->getIriFromResource($event);

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->patch($eventIri, [
                'json' => [
                    'label' => $label = 'New Label',
                    'description' => $description = 'Updated description',
                    'duration' => $duration = 60,
                    'status' => $status = EventStatus::PUBLISHED->value,
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ])
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMatches('label', $label)
            ->assertJsonMatches('description', $description)
            ->assertJsonMatches('duration', $duration)
            ->assertJsonMatches('status', $status)
        ;
    }

    #[Group('patch-endpoints-forbidden')]
    #[Group('patch-events-endpoints-forbidden')]
    public function testPatchForbidden(): void
    {
        $eventIri = $this->getIriFromResource(EventFactory::createOne());

        $this->browser()
            ->actingAs($this->createUser())
            ->patch($eventIri, [
                'json' => [],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN)
        ;
    }

    #[Group('delete-endpoints-success')]
    #[Group('delete-events-endpoints-success')]
    public function testDeleteSuccess(): void
    {
        $eventIri = $this->getIriFromResource(EventFactory::createOne());

        $this->browser()
            ->actingAs($this->createUser(roles: ['ROLE_PLATFORM']))
            ->delete($eventIri)
            ->assertStatus(Response::HTTP_NO_CONTENT)
        ;
    }

    #[Group('delete-endpoints-forbidden')]
    #[Group('delete-events-endpoints-forbidden')]
    public function testDeleteForbidden(): void
    {
        $eventIri = $this->getIriFromResource(EventFactory::createOne());

        $this->browser()
            ->actingAs($this->createUser())
            ->delete($eventIri)
            ->assertStatus(Response::HTTP_FORBIDDEN)
        ;
    }
}
