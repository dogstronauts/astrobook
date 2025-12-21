<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'router' => [
            'default_uri' => param('default_uri'),
        ],
    ]);
    if ('prod' === $containerConfigurator->env()) {
        $containerConfigurator->extension('framework', [
            'router' => [
                'strict_requirements' => null,
            ],
        ]);
    }
};
