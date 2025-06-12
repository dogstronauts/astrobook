<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            PasswordAuthenticatedUserInterface::class => 'auto',
        ],
        'providers' => [
            'user_provider' => [
                'entity' => [
                    'class' => Dogstronauts\AstroBook\Security\Model\User::class,
                    'property' => 'identifier',
                ],
            ],
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/_(profiler|wdt)',
                'security' => false,
            ],
            'main' => [
                'stateless' => true,
                'provider' => 'user_provider',
                'entry_point' => 'jwt',
                'json_login' => [
                    'check_path' => 'authenticate',
                    'username_path' => 'identifier',
                    'password_path' => 'password',
                    'success_handler' => 'lexik_jwt_authentication.handler.authentication_success',
                    'failure_handler' => 'lexik_jwt_authentication.handler.authentication_failure',
                ],
                'jwt' => null,
                'refresh_jwt' => [
                    'check_path' => 'refresh_tokens',
                ],
            ],
        ],
        'access_control' => [
            [
                'path' => '^/(docs|$)',
                'roles' => 'PUBLIC_ACCESS',
            ],
            [
                'path' => '^/(me|authenticate|refresh_tokens)',
                'roles' => 'PUBLIC_ACCESS',
                'method' => 'POST',
            ],
            [
                'path' => '^/events',
                'roles' => 'PUBLIC_ACCESS',
                'method' => 'GET',
            ],
            [
                'path' => '^/',
                'roles' => 'IS_AUTHENTICATED_FULLY',
            ],
        ],
    ]);
    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('security', [
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => [
                    'algorithm' => 'md5',
                    'encode_as_base64' => false,
                    'iterations' => 0,
                ],
            ],
        ]);
    }
};
