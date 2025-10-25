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
    if (\in_array($containerConfigurator->env(), ['dev', 'test'], true)) {
        $containerConfigurator->extension('zenstruck_foundry', [
            'orm' => [
                'reset' => ['mode' => 'migrate'],
            ],
            'persistence' => [
                'flush_once' => true,
            ],
            'make_factory' => [
                'default_namespace' => 'Dogstronauts\AstroBook\Fixtures\Factory',
            ],
            'make_story' => [
                'default_namespace' => 'Dogstronauts\AstroBook\Fixtures\Story',
            ],
            'enable_auto_refresh_with_lazy_objects' => true,
        ]);
    }
};
