<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures\Story;

use Dogstronauts\AstroBook\Events\Model\EventStatus;
use Dogstronauts\AstroBook\Fixtures\Factory\EventFactory;
use Dogstronauts\AstroBook\Fixtures\Factory\UserFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

use function Zenstruck\Foundry\faker;

#[AsFixture(name: 'demo')]
final class DemoStory extends Story
{
    public function build(): void
    {
        // create a user
        UserFactory::createOne(['identifier' => 'demo@demo.fr', 'password' => 'demo1234%']);

        // create few events
        EventFactory::new()->sequence([
            [
                'label' => 'Lunar Bone Expedition',
                'description' => 'Mission to explore and retrieve valuable artifacts from the lunar surface.',
                'startAt' => new \DateTimeImmutable(),
                'duration' => 11520, // 8 days
                'status' => faker()->randomElement(EventStatus::cases()),
            ],
            [
                'label' => 'Mars Fetch Mission',
                'description' => 'Long-term expedition to explore the red planet and retrieve Martian samples.',
                'startAt' => new \DateTimeImmutable('+9 days'),
                'duration' => 17280, // 12 days
                'status' => EventStatus::PUBLISHED,
            ],
            [
                'label' => 'Asteroid Belt Patrol',
                'description' => 'Security mission to monitor and protect the outer reaches of our solar system.',
                'startAt' => new \DateTimeImmutable('+21 days'),
                'duration' => 240, // 4 hours
                'status' => EventStatus::PUBLISHED,
            ],
            [
                'label' => 'Zero-G Tail Wag',
                'description' => 'Demonstration event where dogstronauts test their zero-gravity maneuvering and celebrate with a cosmic tail-wag.',
                'startAt' => new \DateTimeImmutable('+21 days'),
                'duration' => 120, // 2 hours
                'status' => EventStatus::PUBLISHED,
            ],
            [
                'label' => 'Galactic Kennel Conference',
                'description' => 'Interstellar summit gathering top space-trained canines to share findings on alien bones and bone-based propulsion.',
                'startAt' => new \DateTimeImmutable('+22 days'),
                'duration' => 420, // 7 hours
                'status' => EventStatus::DRAFT,
            ],
        ])->create();
    }
}
