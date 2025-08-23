<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Users\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity]
class Contact
{
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    public Ulid $id;

    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $firstname;

    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $lastname;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    public string $email;

    #[ORM\Column(type: Types::STRING, length: 20, unique: true, nullable: true)]
    public ?string $phone = null;

    #[ORM\Embedded(class: Address::class)]
    public Address $address;
}
