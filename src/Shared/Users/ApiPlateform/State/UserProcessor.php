<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared\Users\ApiPlateform\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Dogstronauts\AstroBook\Shared\Users\Model\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * This processor handles user data for creation, update, and deletion operations.
 * For non-deletion operations, it hashes the user's plain password and erases credentials
 * before persisting the entity. For deletion operations, it delegates the removal to the
 * configured remove processor.
 *
 * @implements ProcessorInterface<User, User|void>
 */
final readonly class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        #[Autowire(service: 'api_platform.doctrine.orm.state.remove_processor')]
        private ProcessorInterface $removeProcessor,
    ) {
    }

    /**
     * Processes a User entity based on the given operation.
     *
     * If the operation is a deletion, the removal processor is used.
     * Otherwise, the user's plain password is hashed,
     * and the entity is persisted using the persist processor.
     *
     * @param User      $data         the data to process; must be an instance of User
     * @param Operation $operation    the API Platform operation being executed
     * @param array     $uriVariables URI variables for the operation
     * @param array     $context      additional context for processing
     *
     * @return User|void the processed data, or void if the operation is a deletion
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        assert($data instanceof User);

        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }

        if ($data->plainPassword) {
            $data->password = $this->passwordHasher->hashPassword($data, $data->plainPassword);
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
