<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Resources\Fields\Integration;

use Dogstronauts\AstroBook\Resources\Fields\FieldOptionsResolverChain;
use Dogstronauts\AstroBook\Resources\Model\Field;
use Dogstronauts\AstroBook\Resources\Model\FieldType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * @internal
 */
final class FieldOptionsResolverChainTest extends KernelTestCase
{
    private FieldOptionsResolverChain $resolverChain;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->resolverChain = self::getContainer()->get(FieldOptionsResolverChain::class);
    }

    public function testArrayConfiguratorResolvesAvailableValues(): void
    {
        $field = new Field();
        $field->label = 'Test field';
        $field->type = FieldType::ARRAY;
        $field->options = ['availableValues' => ['A', 'B', 'C']];

        $resolved = $this->resolverChain->resolve($field);

        $this->assertSame(['availableValues' => ['A', 'B', 'C']], $resolved);
    }

    public function testDefaultConfiguratorResolvesHelpAndPlaceholder(): void
    {
        $field = new Field();
        $field->label = 'Default type';
        $field->type = FieldType::STRING;
        $field->options = [
            'help' => 'Texte d’aide',
            'placeholder' => 'Votre nom',
        ];

        $resolved = $this->resolverChain->resolve($field);

        $this->assertSame('Texte d’aide', $resolved['help']);
        $this->assertSame('Votre nom', $resolved['placeholder']);
    }

    public function testInvalidOptionsTriggerException(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $field = new Field();
        $field->label = 'Field sans options valides';
        $field->type = FieldType::ARRAY;
        $field->options = ['availableValues' => 'not_an_array'];

        $this->resolverChain->resolve($field);
    }
}
