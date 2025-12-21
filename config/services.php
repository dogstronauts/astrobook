<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/parameters.php');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('Dogstronauts\AstroBook\\', sprintf('%s/../src/', __DIR__));

    if (in_array($containerConfigurator->env(), ['dev', 'test'], true)) {
        $services
            ->load('Dogstronauts\AstroBook\Fixtures\\', sprintf('%s/../fixtures/', __DIR__))
            ->autowire()
            ->autoconfigure()
        ;
    }
};
