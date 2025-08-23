<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Auth\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Dogstronauts\AstroBook\Auth\ApiPlatform\State\PasswordResetRequestProcessor;
use Dogstronauts\AstroBook\Auth\Repository\PasswordResetRequestRepository;
use Dogstronauts\AstroBook\Users\Model\User;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[ORM\Entity(repositoryClass: PasswordResetRequestRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/auth/password_reset_requests',
            status: Response::HTTP_NO_CONTENT,
            openapi: new Operation(tags: ['Auth']),
            output: false,
            processor: PasswordResetRequestProcessor::class
        ),
    ],
    normalizationContext: ['groups' => ['password-reset-request:read']],
    denormalizationContext: ['groups' => ['password-reset-request:write']],
)]
class PasswordResetRequest
{
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME)]
    #[ORM\GeneratedValue('CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    public Ulid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    public User $user;

    #[Assert\NotBlank]
    #[Serializer\Groups(['password-reset-request:write'])]
    public string $identifier;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 128)]
    #[PasswordStrength(
        minScore: PasswordStrength::STRENGTH_MEDIUM,
    )]
    #[Serializer\Groups(['password-reset-request:write'])]
    public string $newPassword;

    #[Assert\NotBlank]
    #[Assert\IdenticalTo(propertyPath: 'newPassword', message: 'passwords do not match')]
    #[Serializer\Groups(['password-reset-request:write'])]
    public string $confirmPassword;

    #[ORM\Column(type: UlidType::NAME)]
    public Ulid $token;

    #[ORM\Column(type: Types::STRING, length: 128)]
    public string $password;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $expiresAt;
}
