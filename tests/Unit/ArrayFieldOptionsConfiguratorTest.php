<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Unit;

use Dogstronauts\AstroBook\Fields\Configurator\ArrayFieldOptionsConfigurator;
use Dogstronauts\AstroBook\Fields\Model\FieldType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @internal
 */
final class ArrayFieldOptionsConfiguratorTest extends TestCase
{
    public function testSupportsOnlyArrayType(): void
    {
        $configurator = new ArrayFieldOptionsConfigurator();

        foreach (FieldType::cases() as $type) {
            $expected = FieldType::ARRAY === $type;
            $this->assertSame(
                $expected,
                $configurator->supports($type),
                sprintf('Expected supports(%s) to be %s', $type->name, $expected ? 'true' : 'false')
            );
        }
    }

    public function testConfigureRequiresAvailableValues(): void
    {
        $configurator = new ArrayFieldOptionsConfigurator();
        $resolver = new OptionsResolver();

        $configurator->configure($resolver, []);
        $resolved = $resolver->resolve(['availableValues' => ['a', 'b', 'c']]);

        $this->assertEquals(['availableValues' => ['a', 'b', 'c']], $resolved);
    }

    public function testConfigureFailsWithoutAvailableValues(): void
    {
        $this->expectException(MissingOptionsException::class);

        $configurator = new ArrayFieldOptionsConfigurator();
        $resolver = new OptionsResolver();

        $configurator->configure($resolver, []);
        $resolver->resolve();
    }

    public function testConfigureFailsIfAvailableValuesIsNotArray(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $configurator = new ArrayFieldOptionsConfigurator();
        $resolver = new OptionsResolver();

        $configurator->configure($resolver, []);
        $resolver->resolve(['availableValues' => 'not_an_array']);
    }
}
