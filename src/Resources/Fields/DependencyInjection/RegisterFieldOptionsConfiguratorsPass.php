<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Resources\Fields\DependencyInjection;

use Dogstronauts\AstroBook\Resources\Fields\FieldOptionsResolverChain;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterFieldOptionsConfiguratorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(FieldOptionsResolverChain::class)) {
            return;
        }

        $definition = $container->findDefinition(FieldOptionsResolverChain::class);

        $taggedServices = [];

        foreach ($container->findTaggedServiceIds('fields.options_configurator') as $id => $tags) {
            foreach ($tags as $tag) {
                $priority = $tag['priority'] ?? 0;
                $taggedServices[] = ['id' => $id, 'priority' => $priority];
            }
        }

        usort($taggedServices, fn ($a, $b): int => $b['priority'] <=> $a['priority']);

        foreach ($taggedServices as $entry) {
            $definition->addMethodCall('registerConfigurator', [new Reference($entry['id'])]);
        }
    }
}
