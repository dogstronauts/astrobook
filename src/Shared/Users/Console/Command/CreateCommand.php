<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared\Users\Console\Command;

use Dogstronauts\AstroBook\Shared\Users\Event\UserCreationEvent;
use Dogstronauts\AstroBook\Shared\Users\Exception;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

/**
 * Console command to create a new user interactively or via arguments.
 *
 * This command orchestrates user creation by delegating input collection
 * and user creation to specialized services. Supports both interactive
 * and non-interactive modes for better automation capabilities.
 */
#[AsCommand(
    name: 'users:create',
    description: 'Create a new user interactively or via arguments'
)]
final class CreateCommand extends Command
{
    private const array AVAILABLE_ROLES = ['ROLE_PLATFORM'];

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Argument(description: 'User identifier')]
        ?string $identifier = null,
        #[Argument(description: 'User password')]
        ?string $password = null,
        #[Argument(description: 'Comma-separated roles')]
        ?string $roles = null
    ): int {
        $io = new SymfonyStyle($input, $output);

        $io->title('Create a new user');

        try {
            $identifier = $this->getIdentifier($input, $output, $identifier);
            $password = $this->getPassword($input, $output, $password);
            $roles = $this->getRoles($input, $output, $roles);

            $this->dispatchUserCreationEvent($identifier, $password, $roles);

            $io->success(sprintf(
                'User "%s" created successfully with roles: %s',
                $identifier,
                $this->denormalizeRoles($roles)
            ));

            return Command::SUCCESS;
        } catch (Exception $exception) {
            $io->error(sprintf('Error creating user: %s', $exception->getMessage()));
            $io->writeln('Please try again with correct values.');

            return Command::FAILURE;
        }
    }

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp(<<<'HELP'
                Create a new user either interactively or by providing arguments.

                Interactive mode (no arguments):
                  <info>php bin/console users:create</info>

                Non-interactive mode (with arguments):
                  <info>php bin/console users:create user@example.com mypassword ROLE_USER,ROLE_ADMIN</info>
                HELP)
        ;
    }

    private function createQuestion(string $question, callable $validator, ?int $maxAttempts = null): Question
    {
        $question = new Question($question);
        $question->setValidator($validator);
        $question->setMaxAttempts($maxAttempts);

        return $question;
    }

    private function getQuestionHelper(): QuestionHelper
    {
        return $this->getHelper('question');
    }

    private static function identifierValidator(): callable
    {
        return Validation::createCallable(new NotBlank(), new Length(max: 128));
    }

    private function askForIdentifier(InputInterface $input, OutputInterface $output): string
    {
        $question = $this->createQuestion('Enter the user identifier: ', self::identifierValidator());

        return $this->getQuestionHelper()->ask($input, $output, $question);
    }

    private function getIdentifier(InputInterface $input, OutputInterface $output): string
    {
        $identifier = $input->getArgument('identifier');

        if (null === $identifier) {
            return $this->askForIdentifier($input, $output);
        }

        try {
            self::identifierValidator()($identifier);
        } catch (ValidationFailedException $validationFailedException) {
            $output->writeln('<comment>invalid identifier: ' . $validationFailedException->getMessage() . '</comment>');

            return $this->askForIdentifier($input, $output);
        }

        return $identifier;
    }

    private static function passwordValidator(): callable
    {
        return Validation::createCallable(
            new NotBlank(),
            new Length(min: 8, max: 128),
            new PasswordStrength(minScore: PasswordStrength::STRENGTH_MEDIUM),
        );
    }

    private function askForPassword(InputInterface $input, OutputInterface $output): string
    {
        $question = $this->createQuestion('Enter the password: ', self::passwordValidator());
        $question->setHidden(true);
        $question->setHiddenFallback(true);

        return $this->getQuestionHelper()->ask($input, $output, $question);
    }

    private function getPassword(InputInterface $input, OutputInterface $output): string
    {
        $password = $input->getArgument('password');

        if (null === $password) {
            return $this->askForPassword($input, $output);
        }

        try {
            self::passwordValidator()($password);
        } catch (ValidationFailedException $validationFailedException) {
            $output->writeln('<comment>invalid password: ' . $validationFailedException->getMessage() . '</comment>');

            return $this->askForPassword($input, $output);
        }

        return $password;
    }

    private static function rolesValidator(): callable
    {
        return Validation::createCallable(
            new NotBlank(),
            new All([
                new NotBlank(),
                new Regex('/^ROLE_[A-Z_]+$/'),
            ]),
        );
    }

    private function normalizeRoles(string $roles): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $roles))));
    }

    private function denormalizeRoles(array $roles): string
    {
        return implode(',', $roles);
    }

    private function askForRoles(InputInterface $input, OutputInterface $output): array
    {
        $question = $this->createQuestion(
            'Enter roles (comma-separated, e.g., ROLE_USER,ROLE_ADMIN): ',
            self::rolesValidator(),
            3
        );

        $question->setAutocompleterValues(self::AVAILABLE_ROLES);
        $question->setNormalizer(
            fn (string $answer): array => $this->normalizeRoles($answer)
        );

        return $this->getQuestionHelper()->ask($input, $output, $question);
    }

    private function getRoles(InputInterface $input, OutputInterface $output): array
    {
        $roles = $input->getArgument('roles');

        if (null === $roles) {
            return $this->askForRoles($input, $output);
        }

        try {
            self::rolesValidator()($this->normalizeRoles($roles));
        } catch (ValidationFailedException $validationFailedException) {
            $output->writeln('<comment>invalid roles: ' . $validationFailedException->getMessage() . '</comment>');

            return $this->askForRoles($input, $output);
        }

        return $this->normalizeRoles($roles);
    }

    private function dispatchUserCreationEvent(string $identifier, string $password, array $roles): void
    {
        $this->eventDispatcher->dispatch(new UserCreationEvent($identifier, $password, $roles));
    }
}
