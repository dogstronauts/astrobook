<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250621085426 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE event ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE resource_type ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE taxonomy ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE "user" ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE "user" DROP deleted_at
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE resource_type DROP deleted_at
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE "event" DROP deleted_at
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE taxonomy DROP deleted_at
            SQL);
    }
}
