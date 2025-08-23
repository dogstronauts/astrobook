<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Resources\Fields;

use Dogstronauts\AstroBook\Resources\Fields\Configurator\FieldOptionsConfiguratorInterface;
use Dogstronauts\AstroBook\Resources\Model\Field;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldOptionsResolverChain
{
    /** @var FieldOptionsConfiguratorInterface[] */
    private array $configurators = [];

    /** @var array<string, OptionsResolver> */
    private static array $resolverCache = [];

    public function registerConfigurator(FieldOptionsConfiguratorInterface $configurator): void
    {
        $this->configurators[] = $configurator;
    }

    public function resolve(Field $field): array
    {
        $resolver = self::$resolverCache[$field->type->name] ??= (function () use ($field) {
            $resolver = new OptionsResolver();

            foreach ($this->configurators as $configurator) {
                if ($configurator->supports($field->type)) {
                    $configurator->configure($resolver, $field->options);
                }
            }

            return $resolver;
        })();

        return $resolver->resolve($field->options);
    }
}
