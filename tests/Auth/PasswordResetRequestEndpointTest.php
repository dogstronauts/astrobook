<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Auth;

use Dogstronauts\AstroBook\Auth\Model\PasswordResetRequest;
use Dogstronauts\AstroBook\Auth\Repository\PasswordResetRequestRepository;
use Dogstronauts\AstroBook\Tests\Shared\Functional\KernelTestCase;
use Dogstronauts\AstroBook\Users\Model\User;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * @internal
 */
#[Group('endpoints')]
#[Group('auth-endpoints')]
#[Group('auth-password-reset-requests-endpoints')]
final class PasswordResetRequestEndpointTest extends KernelTestCase
{
    use MailerAssertionsTrait;

    protected function setUp(): void
    {
        parent::setUp();

        self::getContainer()->get('mailer.message_logger_listener')->reset();
        $this->getPasswordResetRequestRepository()->createQueryBuilder('prr')->delete()->getQuery()->execute();
    }

    #[Group('post-endpoints-success')]
    #[Group('post-auth-password-reset-requests-endpoint-success')]
    public function testPostSuccess(): void
    {
        $this->createUser();

        $this->browser()
            ->post('/auth/password_reset_requests', [
                'json' => [
                    'identifier' => 'test@example.com',
                    'newPassword' => $new = '3G2wD+jNKd+A36j2',
                    'confirmPassword' => $new,
                ],
            ])
            ->assertStatus(Response::HTTP_NO_CONTENT)
        ;
    }

    #[Group('post-endpoints-validation')]
    #[Group('post-auth-password-reset-requests-endpoint-validation')]
    public function testPostValidationErrorWhenPasswordsMismatch(): void
    {
        $this->createUser();

        $this->browser()
            ->post('/auth/password_reset_requests', [
                'json' => [
                    'identifier' => 'test@example.com',
                    'newPassword' => 'ValidPass123!@#',
                    'confirmPassword' => 'DifferentPass123!@#',
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ;
    }

    public function testRequestCreatesTokenAndSendsEmail(): void
    {
        $user = $this->createUser(identifier: 'astro@example.com');

        $this->browser()
            ->post('/auth/password_reset_requests', [
                'json' => [
                    'identifier' => $user->identifier,
                    'newPassword' => $newPassword = 'StrongP@ssw0rd123',
                    'confirmPassword' => $newPassword,
                ],
            ])
            ->assertStatus(Response::HTTP_NO_CONTENT)
        ;

        $requests = $this->getPasswordResetRequestRepository()->findAll();
        self::assertCount(1, $requests);
        $request = $requests[0];

        self::assertInstanceOf(PasswordResetRequest::class, $request);
        self::assertSame($user->identifier, $request->user->identifier);
        self::assertGreaterThan(new \DateTimeImmutable()->modify('+25 minutes'), $request->expiresAt);
        self::assertLessThanOrEqual(new \DateTimeImmutable()->modify('+30 minutes'), $request->expiresAt);

        $hasher = $this->getPasswordHasherFactory()->getPasswordHasher(User::class);
        self::assertTrue($hasher->verify($request->password, $newPassword));

        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertEmailSubjectContains($email, 'Reset password request confirmation');
        self::assertEmailHtmlBodyContains($email, 'password-reset/confirm');
        self::assertStringContainsString($request->token->toString(), $email->getHtmlBody());
    }

    public function testRequestIsSilentlyIgnoredWhenUserIsUnknown(): void
    {
        $payload = [
            'identifier' => 'unknown@astrobook.com',
            'newPassword' => 'AnotherStr0ngP@ss',
            'confirmPassword' => 'AnotherStr0ngP@ss',
        ];

        $this->browser()
            ->post('/auth/password_reset_requests', ['json' => $payload])
            ->assertStatus(Response::HTTP_NO_CONTENT)
        ;

        self::assertCount(0, $this->getPasswordResetRequestRepository()->findAll());
        self::assertEmailCount(0);
    }

    private function getPasswordResetRequestRepository(): PasswordResetRequestRepository
    {
        return self::getContainer()->get(PasswordResetRequestRepository::class);
    }

    private function getPasswordHasherFactory(): PasswordHasherFactoryInterface
    {
        return self::getContainer()->get(PasswordHasherFactoryInterface::class);
    }
}
