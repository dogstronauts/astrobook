<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Unit;

use Dogstronauts\AstroBook\Fields\DependencyInjection\RegisterFieldOptionsConfiguratorsPass;
use Dogstronauts\AstroBook\Fields\FieldOptionsResolverChain;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @internal
 */
class RegisterFieldOptionsConfiguratorsPassTest extends TestCase
{
    public function testTaggedServicesAreAddedToFieldOptionsResolverChain(): void
    {
        $container = new ContainerBuilder();

        $chainDefinition = new Definition();
        $container->setDefinition(FieldOptionsResolverChain::class, $chainDefinition);

        $configurator1 = new Definition();
        $configurator1->addTag('fields.options_configurator');
        $container->setDefinition('field_configurator_1', $configurator1);

        $configurator2 = new Definition();
        $configurator2->addTag('fields.options_configurator');
        $container->setDefinition('field_configurator_2', $configurator2);

        $pass = new RegisterFieldOptionsConfiguratorsPass();
        $this->assertInstanceOf(CompilerPassInterface::class, $pass);
        $pass->process($container);

        $calls = $chainDefinition->getMethodCalls();

        $this->assertCount(2, $calls);
        $this->assertSame('registerConfigurator', $calls[0][0]);
        $this->assertEquals('field_configurator_1', (string) $calls[0][1][0]);

        $this->assertSame('registerConfigurator', $calls[1][0]);
        $this->assertEquals('field_configurator_2', (string) $calls[1][1][0]);
    }
}
