<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Security\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshTokenRepository;
use Gesdinet\JWTRefreshTokenBundle\Model\AbstractRefreshToken;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;

/**
 * Represents a refresh token used for JWT authentication in the system.
 *
 * This entity extends the base refresh token provided by the GesdinetJWTRefreshTokenBundle,
 * and is responsible for storing and managing the lifecycle of refresh tokens.
 * Refresh tokens are used to obtain new access tokens when the current ones expire,
 * ensuring secure and continuous authentication.
 */
#[ORM\Entity(repositoryClass: RefreshTokenRepository::class)]
class RefreshToken extends AbstractRefreshToken
{
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME)]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    #[ORM\GeneratedValue('CUSTOM')]
    public $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    public $refreshToken;

    #[ORM\Column(name: 'identifier', type: Types::STRING, length: 128)]
    public $username;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public $valid;
}
