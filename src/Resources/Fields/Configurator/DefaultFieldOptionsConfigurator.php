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
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultFieldOptionsConfigurator implements FieldOptionsConfiguratorInterface
{
    public function supports(FieldType $type): bool
    {
        return true;
    }

    public function configure(OptionsResolver $resolver, array $options): void
    {
        $resolver->setDefined(['help', 'placeholder']);
        $resolver->setAllowedTypes('placeholder', ['null', 'string']);
        $resolver->setAllowedTypes('help', ['null', 'string']);
    }
}
