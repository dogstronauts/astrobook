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
    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('dama_doctrine_test', [
            'enable_static_connection' => true,
            'enable_static_meta_data_cache' => true,
            'enable_static_query_cache' => true,
        ]);
    }
};
