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
    $containerConfiguratorParameters = $containerConfigurator->parameters();

    $containerConfiguratorParameters
        ->set('env(APP_ENV)', 'dev')
        ->set('env(APP_SECRET)', '634ce8ec464ffdac781c6c986e1b4769')
        ->set('env(APP_SHARE_DIR)', 'var/share')
        ->set('env(DATABASE_URL)', 'postgresql://user:password@app-database:5432/dbname?serverVersion=18&charset=utf8')
        ->set('env(CORS_ALLOW_ORIGIN)', '^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$')
        ->set('env(JWT_SECRET_KEY)', sprintf('%s/%s', param('kernel.project_dir'), 'config/jwt/private.pem'))
        ->set('env(JWT_PUBLIC_KEY)', sprintf('%s/%s', param('kernel.project_dir'), 'config/jwt/public.pem'))
        ->set('env(JWT_PASSPHRASE)', '2adb39303a2d4d4b912047562ffa151645fdd0c04da0f83c9742830aebc7214f')
        ->set('env(MAILER_DSN)', 'smtp://app-mailer:1025')
        ->set('env(MAILER_FROM_DEFAULT)', 'Intersideral Void <intersideral-void@dogstronauts.com>')
        ->set('env(DEFAULT_URI)', 'http://localhost')
    ;

    $containerConfiguratorParameters
        ->set('app_env', '%env(APP_ENV)%')
        ->set('app_secret', '%env(APP_SECRET)%')
        ->set('app_share_dir', '%env(APP_SHARE_DIR)%')
        ->set('database_url', '%env(DATABASE_URL)%')
        ->set('cors_allow_origin', '%env(CORS_ALLOW_ORIGIN)%')
        ->set('jwt_secret_key', '%env(resolve:JWT_SECRET_KEY)%')
        ->set('jwt_public_key', '%env(resolve:JWT_PUBLIC_KEY)%')
        ->set('jwt_passphrase', '%env(resolve:JWT_PASSPHRASE)%')
        ->set('mailer.dsn', '%env(MAILER_DSN)%')
        ->set('mailer.from_default', '%env(MAILER_FROM_DEFAULT)%')
        ->set('default_uri', '%env(DEFAULT_URI)%')
    ;

    if ('prod' === $containerConfigurator->env()) {
        $containerConfiguratorParameters
            ->set('.container.dumper.inline_factories', true)
            ->set('.container.dumper.inline_class_loader', true)
        ;
    }
};
