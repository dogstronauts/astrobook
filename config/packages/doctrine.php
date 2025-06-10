<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'url' => '%env(resolve:DATABASE_URL)%',
            'profiling_collect_backtrace' => '%kernel.debug%',
            'use_savepoints' => true,
        ],
        'orm' => [
            'auto_generate_proxy_classes' => true,
            'enable_lazy_ghost_objects' => true,
            'report_fields_where_declared' => true,
            'validate_xml_mapping' => true,
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'identity_generation_preferences' => [
                PostgreSQLPlatform::class => 'identity',
            ],
            'auto_mapping' => true,
            'mappings' => [
                'Shared' => [
                    'type' => 'attribute',
                    'is_bundle' => false,
                    'dir' => '%kernel.project_dir%/src/Shared/Model',
                    'prefix' => 'Dogstronauts\AstroBook\Shared\Model',
                    'alias' => 'Shared',
                ],
                'Security' => [
                    'type' => 'attribute',
                    'is_bundle' => false,
                    'dir' => '%kernel.project_dir%/src/Security/Model',
                    'prefix' => 'Dogstronauts\AstroBook\Security\Model',
                    'alias' => 'Security',
                ],
                'Resources' => [
                    'type' => 'attribute',
                    'is_bundle' => false,
                    'dir' => '%kernel.project_dir%/src/Resources/Model',
                    'prefix' => 'Dogstronauts\AstroBook\Resources\Model',
                    'alias' => 'Resources',
                ],
                'Events' => [
                    'type' => 'attribute',
                    'is_bundle' => false,
                    'dir' => '%kernel.project_dir%/src/Events/Model',
                    'prefix' => 'Dogstronauts\AstroBook\Events\Model',
                    'alias' => 'Events',
                ],
            ],
            'controller_resolver' => [
                'auto_mapping' => false,
            ],
        ],
    ]);
    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('doctrine', [
            'dbal' => [
                'dbname_suffix' => '_test%env(default::TEST_TOKEN)%',
            ],
        ]);
    }

    if ('prod' === $containerConfigurator->env()) {
        $containerConfigurator->extension('doctrine', [
            'orm' => [
                'auto_generate_proxy_classes' => false,
                'proxy_dir' => '%kernel.build_dir%/doctrine/orm/Proxies',
                'query_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.system_cache_pool',
                ],
                'result_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.result_cache_pool',
                ],
            ],
        ]);
        $containerConfigurator->extension('framework', [
            'cache' => [
                'pools' => [
                    'doctrine.result_cache_pool' => [
                        'adapter' => 'cache.app',
                    ],
                    'doctrine.system_cache_pool' => [
                        'adapter' => 'cache.system',
                    ],
                ],
            ],
        ]);
    }
};
