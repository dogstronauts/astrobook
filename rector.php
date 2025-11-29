<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/fixtures',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withTypeCoverageLevel(10)
    ->withDeadCodeLevel(10)
    ->withCodeQualityLevel(10)
    ->withCodingStyleLevel(10)
    ->withComposerBased(doctrine: true, phpunit: true, symfony: true)
    ->withAttributesSets(symfony: true, doctrine: true, phpunit: true)
    ->withSets([
        LevelSetList::UP_TO_PHP_85,
    ])
;
