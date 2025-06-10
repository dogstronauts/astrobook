<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures\Factory;

use Dogstronauts\AstroBook\Events\Enum\EventStatus;
use Dogstronauts\AstroBook\Events\Model\Event;
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
        $startDateTime = self::faker()->dateTimeBetween('-1 week', '+1 week');
        $endDateTime = self::faker()->dateTimeBetween('+2 week', '+3 week');

        $startImmutable = \DateTimeImmutable::createFromMutable($startDateTime);
        $endImmutable = \DateTimeImmutable::createFromMutable($endDateTime);

        // Duration in minutes
        $interval = $startImmutable->diff($endImmutable);
        $minutes = $interval->days * 24 * 60
            + $interval->h * 60
            + $interval->i;

        return [
            'label' => self::faker()->sentence(3),
            'description' => self::faker()->optional()->paragraph(),
            'startAt' => $startImmutable,
            'endAt' => $endImmutable,
            'duration' => $minutes,
            'status' => self::faker()->randomElement(EventStatus::cases()),
        ];
    }
}
