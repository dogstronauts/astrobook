<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\ApiPlatform\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Server;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator('api_platform.openapi.factory')]
final readonly class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $server = new Server('{scheme}://{host}:{port}', '', new \ArrayObject([
            'scheme' => new \ArrayObject(['default' => 'http', 'description' => 'scheme']),
            'host' => new \ArrayObject(['default' => 'localhost', 'description' => 'host']),
            'port' => new \ArrayObject(['default' => '8080', 'description' => 'port']),
        ]));

        return $openApi->withServers([$server]);
    }
}
