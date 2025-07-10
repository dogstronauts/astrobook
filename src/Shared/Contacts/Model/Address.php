<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared\Contacts\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[Embeddable]
class Address
{
    #[ORM\Column(type: Types::JSON)]
    private array $data = [];

    #[Serializer\Groups(['address:read', 'address:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 128)]
    public ?string $street {
        get {
            return $this->data['street'] ?? null;
        }
        set {
            $this->data['street'] = $value;
        }
    }

    #[Serializer\Groups(['address:read', 'address:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    public ?string $city {
        get {
            return $this->data['city'] ?? null;
        }
        set {
            $this->data['city'] = $value;
        }
    }

    #[Serializer\Groups(['address:read', 'address:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 16)]
    public ?string $postalCode {
        get {
            return $this->data['postalCode'] ?? null;
        }
        set {
            $this->data['postalCode'] = $value;
        }
    }

    #[Serializer\Groups(['address:read', 'address:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    public ?string $country {
        get {
            return $this->data['country'] ?? null;
        }
        set {
            $this->data['country'] = $value;
        }
    }
}
