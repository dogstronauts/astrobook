<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Tests\Users\Console\Command;

use Dogstronauts\AstroBook\Users\Event\UserCreationEvent;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
#[Group('users')]
#[Group('commands')]
final class CreateCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    private array $dispatchedEvents = [];

    protected function setUp(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $eventDispatcher = self::getContainer()->get('event_dispatcher');

        $eventDispatcher->addListener(UserCreationEvent::class, function (UserCreationEvent $event): void {
            $this->dispatchedEvents[] = $event;
        });

        $command = $application->find('users:create');
        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown(): void
    {
        $this->dispatchedEvents = [];
        parent::tearDown();
    }

    public function testExecuteWithAllArgumentsSuccess(): void
    {
        $this->commandTester->execute([
            'identifier' => 'test@example.com',
            'password' => 'StrongP@ssw0rd123',
            'roles' => 'ROLE_PLATFORM,ROLE_USER',
        ]);

        $this->assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('User "test@example.com" created successfully', $output);
        $this->assertStringContainsString('ROLE_PLATFORM,ROLE_USER', $output);

        $this->assertCount(1, $this->dispatchedEvents);
        $event = $this->dispatchedEvents[0];
        $this->assertInstanceOf(UserCreationEvent::class, $event);
        $this->assertSame('test@example.com', $event->identifier);
        $this->assertSame('StrongP@ssw0rd123', $event->plainPassword);
        $this->assertSame(['ROLE_PLATFORM', 'ROLE_USER'], $event->roles);
    }

    public function testExecuteWithSingleRole(): void
    {
        $this->commandTester->setInputs([
            'admin@example.com',
            'AdminP@ss123',
            'ROLE_PLATFORM',
        ]);

        $this->commandTester->execute([
            'identifier' => 'admin@example.com',
            'password' => 'AdminP@ss123',
            'roles' => 'ROLE_PLATFORM',
        ]);

        $this->assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('User "admin@example.com" created successfully', $output);
        $this->assertStringContainsString('ROLE_PLATFORM', $output);

        $this->assertCount(1, $this->dispatchedEvents);
        $event = $this->dispatchedEvents[0];
        $this->assertInstanceOf(UserCreationEvent::class, $event);
        $this->assertSame(['ROLE_PLATFORM'], $event->roles);
    }

    public function testExecuteWithInvalidIdentifierFallbackToInteractive(): void
    {
        $this->commandTester->setInputs([
            'valid@example.com',
            'ValidP@ssw0rd123',
            'ROLE_PLATFORM',
        ]);

        $this->commandTester->execute([
            'identifier' => '',
            'password' => 'ValidP@ssw0rd123',
            'roles' => 'ROLE_PLATFORM',
        ]);

        $this->assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('invalid identifier', $output);
        $this->assertStringContainsString('User "valid@example.com" created successfully', $output);
    }

    public function testExecuteWithInvalidPasswordFallbackToInteractive(): void
    {
        $this->commandTester->setInputs([
            'StrongP@ssw0rd123',
            'ROLE_PLATFORM',
        ]);

        $this->commandTester->execute([
            'identifier' => 'test@example.com',
            'password' => 'weak',
            'roles' => 'ROLE_PLATFORM',
        ]);

        $this->assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('invalid password', $output);
        $this->assertStringContainsString('User "test@example.com" created successfully', $output);
    }

    public function testExecuteWithInvalidRolesFallbackToInteractive(): void
    {
        $this->commandTester->setInputs([
            'ROLE_PLATFORM',
        ]);

        $this->commandTester->execute([
            'identifier' => 'test@example.com',
            'password' => 'ValidP@ssw0rd123',
            'roles' => 'INVALID_ROLE',
        ]);

        $this->assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('invalid roles', $output);
        $this->assertStringContainsString('User "test@example.com" created successfully', $output);
    }

    public function testExecuteInteractiveModeSuccess(): void
    {
        $this->commandTester->setInputs([
            'interactive@example.com',
            'InteractiveP@ss123',
            'ROLE_PLATFORM',
        ]);

        $this->commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Create a new user', $output);
        $this->assertStringContainsString('Enter the user identifier:', $output);
        $this->assertStringContainsString('Enter the password:', $output);
        $this->assertStringContainsString('Enter roles', $output);
        $this->assertStringContainsString('User "interactive@example.com" created successfully', $output);

        $this->assertCount(1, $this->dispatchedEvents);
        $event = $this->dispatchedEvents[0];
        $this->assertInstanceOf(UserCreationEvent::class, $event);
        $this->assertSame('interactive@example.com', $event->identifier);
    }

    public function testExecuteWithMultipleValidationFailures(): void
    {
        $this->commandTester->setInputs([
            'valid@example.com',
            'ValidP@ssw0rd123',
            'ROLE_PLATFORM',
        ]);

        $this->commandTester->execute([
            'identifier' => '',
            'password' => 'weak',
            'roles' => 'invalid_role_format',
        ]);

        $this->assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('invalid identifier', $output);
        $this->assertStringContainsString('invalid password', $output);
        $this->assertStringContainsString('invalid roles', $output);
        $this->assertStringContainsString('User "valid@example.com" created successfully', $output);
    }

    public function testExecuteWithRolesContainingSpaces(): void
    {
        $this->commandTester->execute([
            'identifier' => 'test@example.com',
            'password' => 'ValidP@ssw0rd123',
            'roles' => 'ROLE_PLATFORM, ROLE_USER , ROLE_ADMIN',
        ]);

        $this->assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());

        $this->assertCount(1, $this->dispatchedEvents);
        $event = $this->dispatchedEvents[0];
        $this->assertInstanceOf(UserCreationEvent::class, $event);
        $this->assertSame(['ROLE_PLATFORM', 'ROLE_USER', 'ROLE_ADMIN'], $event->roles);
    }

    public function testExecuteWithEmptyRolesAfterNormalization(): void
    {
        $this->commandTester->setInputs([
            'ROLE_PLATFORM',
        ]);

        $this->commandTester->execute([
            'identifier' => 'test@example.com',
            'password' => 'ValidP@ssw0rd123',
            'roles' => ', , ,',
        ]);

        $this->assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('invalid roles', $output);
        $this->assertStringContainsString('User "test@example.com" created successfully', $output);
    }
}
