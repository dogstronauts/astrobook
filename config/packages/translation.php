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
        'default_locale' => 'en',
        'enabled_locales' => ['en', 'fr'],
        'set_locale_from_accept_language' => true,
        'translator' => [
            'default_path' => sprintf('%s/translations', param('kernel.project_dir')),
            'providers' => null,
            'fallbacks' => [
                'en',
            ],
        ],
    ]);
};
