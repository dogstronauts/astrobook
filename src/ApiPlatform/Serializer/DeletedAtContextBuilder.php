<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\ApiPlatform\Serializer;

use ApiPlatform\State\SerializerContextBuilderInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[AsDecorator('api_platform.serializer.context_builder')]
final readonly class DeletedAtContextBuilder implements SerializerContextBuilderInterface
{
    private const string DELETED_AT_READ_GROUP = 'deleted-at:read';

    private const string DELETED_AT_WRITE_GROUP = 'deleted-at:write';

    private const string ROLE_ALLOWED = 'ROLE_PLATFORM';

    public function __construct(
        private SerializerContextBuilderInterface $decorated,
        private AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        if (isset($context['groups']) && $this->authorizationChecker->isGranted(self::ROLE_ALLOWED)) {
            $context['groups'][] = $normalization
                ? self::DELETED_AT_READ_GROUP
                : self::DELETED_AT_WRITE_GROUP;
        }

        return $context;
    }
}
