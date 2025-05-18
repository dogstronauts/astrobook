<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Functional;

use ApiPlatform\Metadata\IriConverterInterface;
use Dogstronauts\AstroBook\Fixtures\Factory\UserFactory;
use Dogstronauts\AstroBook\Security\Model\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as baseKernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;

/**
 * @internal
 */
abstract class KernelTestCase extends baseKernelTestCase
{
    use Factories;
    use HasBrowser;

    protected function createUser(): User
    {
        return UserFactory::createOne(['identifier' => 'test@example.com', 'password' => '$3CR3T'])->_real();
    }

    protected function getIriFromResource(object $resource): ?string
    {
        /** @var IriConverterInterface $iriConverter */
        $iriConverter = static::getContainer()->get('api_platform.iri_converter');

        return $iriConverter->getIriFromResource($resource);
    }
}
