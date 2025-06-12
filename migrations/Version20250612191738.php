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

final class Version20250612191738 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                CREATE TABLE "event" (id UUID NOT NULL, label VARCHAR(64) NOT NULL, description TEXT DEFAULT NULL, start_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(9) NOT NULL, duration INT NOT NULL, PRIMARY KEY(id))
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                DROP TABLE "event"
            SQL);
    }
}
