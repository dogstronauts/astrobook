<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Resources\Fields\Configurator;

use Dogstronauts\AstroBook\Resources\Model\FieldType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[AutoconfigureTag(name: 'fields.options_configurator')]
interface FieldOptionsConfiguratorInterface
{
    public function supports(FieldType $type): bool;

    public function configure(OptionsResolver $resolver, array $options): void;
}
