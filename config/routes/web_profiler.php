<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    if ('dev' === $routingConfigurator->env()) {
        $routingConfigurator->import('@WebProfilerBundle/Resources/config/routing/wdt.php')
            ->prefix('/_wdt')
        ;
        $routingConfigurator->import('@WebProfilerBundle/Resources/config/routing/profiler.php')
            ->prefix('/_profiler')
        ;
    }
};
