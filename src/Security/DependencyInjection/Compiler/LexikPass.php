<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Security\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass to expose Lexik JWT Authentication parameters for API Platform.
 *
 * This pass extracts key configuration values from the JSON login authenticator
 * in the security firewalls and sets them as container parameters. These parameters
 * (API path, username, and password) are then used by API Platform to configure JWT authentication.
 */
final class LexikPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->addApiPlatformParameters($container);
    }

    /**
     * Extracts the API path, username, and password values from the JSON login authenticator
     * configuration in the security firewalls and sets them as container parameters.
     *
     * If the username or password value is not defined, default values ('username' or 'password') are used.
     *
     * @param ContainerBuilder $container the container builder
     */
    private function addApiPlatformParameters(ContainerBuilder $container): void
    {
        $checkPath = null;
        $usernamePath = null;
        $passwordPath = null;
        $firewalls = $container->getParameter('security.firewalls');

        foreach ($firewalls as $firewallName) {
            if ($container->hasDefinition('security.authenticator.json_login.' . $firewallName)) {
                $firewallOptions = $container->getDefinition('security.authenticator.json_login.' . $firewallName)->getArgument(4);
                $checkPath = $firewallOptions['check_path'];
                $usernamePath = $firewallOptions['username_path'];
                $passwordPath = $firewallOptions['password_path'];
                break;
            }
        }

        $container->setParameter('lexik_jwt_authentication.api_platform.path', $checkPath);
        $container->setParameter('lexik_jwt_authentication.api_platform.username', $usernamePath ?? 'username');
        $container->setParameter('lexik_jwt_authentication.api_platform.password', $passwordPath ?? 'password');
    }
}
