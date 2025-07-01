<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Integration;

use Dogstronauts\AstroBook\Fields\Configurator\ArrayFieldOptionsConfigurator;
use Dogstronauts\AstroBook\Fields\Configurator\DefaultFieldOptionsConfigurator;
use Dogstronauts\AstroBook\Fields\FieldOptionsResolverChain;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 */
final class RegisterFieldOptionsConfiguratorsPassTest extends KernelTestCase
{
    public function testFieldOptionsConfiguratorsAreRegisteredInChain(): void
    {
        $container = self::getContainer();

        $chain = $container->get(FieldOptionsResolverChain::class);

        $reflection = new \ReflectionClass($chain);
        $property = $reflection->getProperty('configurators');

        $configurators = $property->getValue($chain);

        $this->assertNotEmpty($configurators);

        $this->assertTrue($this->containsConfigurator(ArrayFieldOptionsConfigurator::class, $configurators));

        $this->assertTrue($this->containsConfigurator(DefaultFieldOptionsConfigurator::class, $configurators));
    }

    private function containsConfigurator(string $expectedClass, array $configurators): bool
    {
        return array_any($configurators, fn ($configurator): bool => $configurator instanceof $expectedClass);
    }
}
