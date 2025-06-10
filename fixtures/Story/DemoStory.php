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
use Zenstruck\Foundry\Story;

final class DemoStory extends Story
{
    public function build(): void
    {
        // create a user
        $userProxy = UserFactory::createOne(['identifier' => 'demo@demo.fr', 'password' => 'demo1234%']);

        $events = EventFactory::createMany(5);
    }
}
