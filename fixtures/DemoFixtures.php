<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Dogstronauts\AstroBook\Fixtures\Story\DemoStory;

final class DemoFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DemoStory::load();
    }
}
