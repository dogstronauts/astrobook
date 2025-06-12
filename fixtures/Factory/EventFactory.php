<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures\Factory;

use Dogstronauts\AstroBook\Events\Model\Event;
use Dogstronauts\AstroBook\Events\Model\EventStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @internal
 *
 * @extends PersistentProxyObjectFactory<Event>
 */
class EventFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Event::class;
    }

    protected function defaults(): array
    {
        return [
            'label' => self::faker()->sentence(3),
            'description' => self::faker()->optional()->paragraph(),
            'startAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 years', '+1 years')),
            'duration' => self::faker()->numberBetween(30, 1440),
            'status' => self::faker()->randomElement(EventStatus::cases()),
        ];
    }
}
