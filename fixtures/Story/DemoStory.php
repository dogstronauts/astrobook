<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures\Story;

use Dogstronauts\AstroBook\Fixtures\Factory\EventFactory;
use Dogstronauts\AstroBook\Fixtures\Factory\UserFactory;
use function Zenstruck\Foundry\faker;
use Zenstruck\Foundry\Story;

final class DemoStory extends Story
{
    public function build(): void
    {
        // create a user
        $userProxy = UserFactory::createOne(['identifier' => 'demo@demo.fr', 'password' => 'demo1234%']);

        // create few events
        $events = [
            'Lunar Bone Expedition' => 'Mission to explore and retrieve valuable artifacts from the lunar surface.',
            'Mars Fetch Mission'    => 'Long-term expedition to explore the red planet and retrieve Martian samples.',
            'Asteroid Belt Patrol'  => 'Security mission to monitor and protect the outer reaches of our solar system.',
            'Zero-G Tail Wag'             => 'Demonstration event where dogstronauts test their zero-gravity maneuvering and celebrate with a cosmic tail-wag.',
            'Galactic Kennel Conference'  => 'Interstellar summit gathering top space-trained canines to share findings on alien bones and bone-based propulsion.',
        ];

        foreach ($events as $label => $description) {
            EventFactory::createOne([
                'label'       => $label,
                'description' => $description,
                'startAt'     => new \DateTimeImmutable('+1 days'),
                'endAt'     => new \DateTimeImmutable('+3 days'),
                'status'      => faker()->randomElement(\Dogstronauts\AstroBook\Events\Enum\EventStatus::cases()),
            ]);
        }
    }
}
