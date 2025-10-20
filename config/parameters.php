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
    $parameters = $containerConfigurator->parameters();

    $parameters->set('env(APP_ENV)', 'dev');

    $parameters->set('env(APP_SECRET)', '634ce8ec464ffdac781c6c986e1b4769');

    $parameters->set('env(DATABASE_URL)', 'postgresql://user:password@app-database:5432/dbname?serverVersion=18&charset=utf8');

    $parameters->set('env(CORS_ALLOW_ORIGIN)', '^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$');

    $parameters->set('env(JWT_SECRET_KEY)', '%kernel.project_dir%/config/jwt/private.pem');

    $parameters->set('env(JWT_PUBLIC_KEY)', '%kernel.project_dir%/config/jwt/public.pem');

    $parameters->set('env(JWT_PASSPHRASE)', '2adb39303a2d4d4b912047562ffa151645fdd0c04da0f83c9742830aebc7214f');

    $parameters->set('env(MAILER_DSN)', 'smtp://app-mailer:1025');

    $parameters->set('env(MAILER_FROM_DEFAULT)', 'Intersideral Void <intersideral-void@dogstronauts.com>');

    $parameters->set('env(DEFAULT_URI)', 'http://localhost');

    $parameters->set('mailer.dsn', '%env(MAILER_DSN)%');

    $parameters->set('mailer.from_default', '%env(MAILER_FROM_DEFAULT)%');

    if ('prod' === $containerConfigurator->env()) {
        $parameters->set('.container.dumper.inline_factories', true);
        $parameters->set('.container.dumper.inline_class_loader', true);
    }
};
