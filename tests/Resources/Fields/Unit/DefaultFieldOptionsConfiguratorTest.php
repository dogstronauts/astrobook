<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Resources\Fields\Unit;

use Dogstronauts\AstroBook\Resources\Fields\Configurator\DefaultFieldOptionsConfigurator;
use Dogstronauts\AstroBook\Resources\Model\FieldType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @internal
 */
final class DefaultFieldOptionsConfiguratorTest extends TestCase
{
    public function testSupportsAlwaysReturnsTrue(): void
    {
        $configurator = new DefaultFieldOptionsConfigurator();

        foreach (FieldType::cases() as $type) {
            $this->assertTrue($configurator->supports($type));
        }
    }

    public function testConfigureResolvesHelpAndPlaceholder(): void
    {
        $configurator = new DefaultFieldOptionsConfigurator();
        $resolver = new OptionsResolver();

        $configurator->configure($resolver, []);
        $resolved = $resolver->resolve([
            'help' => 'Texte d’aide',
            'placeholder' => 'Entrez votre nom',
        ]);

        $this->assertSame('Texte d’aide', $resolved['help']);
        $this->assertSame('Entrez votre nom', $resolved['placeholder']);
    }

    public function testConfigureResolvesOnlyHelp(): void
    {
        $configurator = new DefaultFieldOptionsConfigurator();
        $resolver = new OptionsResolver();

        $configurator->configure($resolver, []);
        $resolved = $resolver->resolve([
            'help' => 'Juste de l’aide',
        ]);

        $this->assertSame('Juste de l’aide', $resolved['help']);
        $this->assertArrayNotHasKey('placeholder', $resolved);
    }

    public function testConfigureResolvesOnlyPlaceholder(): void
    {
        $configurator = new DefaultFieldOptionsConfigurator();
        $resolver = new OptionsResolver();

        $configurator->configure($resolver, []);
        $resolved = $resolver->resolve([
            'placeholder' => 'Choisissez une option',
        ]);

        $this->assertSame('Choisissez une option', $resolved['placeholder']);
        $this->assertArrayNotHasKey('help', $resolved);
    }

    public function testConfigureFailsIfHelpIsInvalidType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $configurator = new DefaultFieldOptionsConfigurator();
        $resolver = new OptionsResolver();

        $configurator->configure($resolver, []);
        $resolver->resolve(['help' => false]);
    }

    public function testConfigureFailsIfPlaceholderIsInvalidType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $configurator = new DefaultFieldOptionsConfigurator();
        $resolver = new OptionsResolver();

        $configurator->configure($resolver, []);
        $resolver->resolve(['placeholder' => ['array_not_allowed']]);
    }

    public function testConfigureFailsWithUnknownOption(): void
    {
        $this->expectException(UndefinedOptionsException::class);

        $configurator = new DefaultFieldOptionsConfigurator();
        $resolver = new OptionsResolver();

        $configurator->configure($resolver, []);
        $resolver->resolve(['nonexistent_option' => 'valeur']);
    }
}
