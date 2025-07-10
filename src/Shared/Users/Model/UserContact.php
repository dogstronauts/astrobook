<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Shared\Users\Model;

use Doctrine\ORM\Mapping as ORM;
use Dogstronauts\AstroBook\Shared\Contacts\Model\Contact;
use Dogstronauts\AstroBook\Shared\Contacts\Model\ContactType;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Entity]
class UserContact
{
    #[ORM\Column(length: 32, enumType: ContactType::class)]
    #[Serializer\Groups(['user-contact:read', 'user-contact:write'])]
    public ContactType $type;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Serializer\Groups(['user-contact:read', 'user-contact:write'])]
    public User $user;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Contact::class, cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Serializer\Groups(['user-contact:read', 'user-contact:write'])]
    public Contact $contact;
}
