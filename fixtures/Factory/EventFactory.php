<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) Damien Lebon <damienlebon.ifpa@gmail.com>
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
        $start = self::faker()->dateTimeBetween('-1 week', '+1 week');
        $end = self::faker()->dateTimeBetween('+2 week', '+3 week');
        $duration = self::faker()->numberBetween(30, 480);

        return [
            'label'       => self::faker()->sentence(3),
            'description' => self::faker()->optional()->paragraph(),
            'startAt'     => \DateTimeImmutable::createFromMutable($start),
            'endAt'     => \DateTimeImmutable::createFromMutable($end),
            'duration'    => $duration,
            'status'      => self::faker()->randomElement(EventStatus::cases()),
        ];
    }
}
