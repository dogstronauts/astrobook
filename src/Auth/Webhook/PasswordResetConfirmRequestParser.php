<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Auth\Webhook;

use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\MethodRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\PathRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\QueryParameterRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Webhook\Client\AbstractRequestParser;

final class PasswordResetConfirmRequestParser extends AbstractRequestParser
{
    protected function getRequestMatcher(): RequestMatcherInterface
    {
        return new ChainRequestMatcher([
            new PathRequestMatcher('^/_webhook/password-reset/confirm$'),
            new MethodRequestMatcher('GET'),
            new QueryParameterRequestMatcher('reset_token'),
        ]);
    }

    /**
     * @throws JsonException
     */
    protected function doParse(Request $request, #[\SensitiveParameter] string $secret): ?RemoteEvent
    {
        return new RemoteEvent(
            name: 'password.reset.confirmed',
            id: $resetToken = $request->query->get('reset_token'),
            payload: ['resetToken' => $resetToken]
        );
    }
}
