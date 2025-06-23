<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Auth\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Dogstronauts\AstroBook\Auth\Model\PasswordResetRequest;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

/**
 * @extends ServiceEntityRepository<PasswordResetRequest>
 */
class PasswordResetRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetRequest::class);
    }

    public function findNotExpired(Ulid $token): ?PasswordResetRequest
    {
        $expiredAtToken = $this->truncateToMilliseconds($token->getDateTime());

        return $this->createQueryBuilder('prr')
            ->andWhere('prr.token = :token')
            ->andWhere('prr.expiresAt = :expires_at_token')
            ->andWhere('prr.expiresAt > :now')
            ->setParameter('token', $token, UlidType::NAME)
            ->setParameter('expires_at_token', $expiredAtToken)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    private function truncateToMilliseconds(\DateTimeImmutable $dateTimeImmutable): \DateTimeImmutable
    {
        $milliseconds = (int) $dateTimeImmutable->format('Uv');
        $seconds = (int) floor($milliseconds / 1000);
        $microseconds = ($milliseconds % 1000) * 1000;

        return new \DateTimeImmutable()->setTimestamp($seconds)->modify(sprintf('+%d microseconds', $microseconds));
    }
}
