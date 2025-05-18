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
final class GesdinetPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->addOpenApiFactory($container);
    }

    /**
     * Extracts the API path, username, and password values from the JSON login authenticator
     * configuration in the security firewalls and sets them as container parameters.
     *
     * If the username or password value is not defined, default values ('username' or 'password') are used.
     *
     * @param ContainerBuilder $container the container builder
     */
    private function addOpenApiFactory(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('gesdinet_jwt_refresh.api_platform.openapi.factory') || !$container->hasParameter('security.firewalls')) {
            return;
        }

        $checkPath = null;
        $firewalls = $container->getParameter('security.firewalls');
        foreach ($firewalls as $firewallName) {
            if ($container->hasDefinition('security.authenticator.refresh_jwt.' . $firewallName)) {
                $firewallOptions = $container->getDefinition('security.authenticator.refresh_jwt.' . $firewallName)->getArgument(6);
                $checkPath = $firewallOptions['check_path'];
                break;
            }
        }

        $openApiFactoryDefinition = $container->getDefinition('gesdinet_jwt_refresh.api_platform.openapi.factory');

        $checkPathArg = $openApiFactoryDefinition->getArgument(0);

        if (!$checkPath && !$checkPathArg) {
            $container->removeDefinition('gesdinet_jwt_refresh.api_platform.openapi.factory');

            return;
        }

        if (!$checkPathArg) {
            $openApiFactoryDefinition->replaceArgument(1, $checkPath);
        }
    }
}
