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

final class Version20250609234952 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                CREATE TABLE taxonomy (id UUID NOT NULL, label VARCHAR(32) NOT NULL, description TEXT DEFAULT NULL, parent_id UUID DEFAULT NULL, PRIMARY KEY(id))
            SQL);
        $this->addSql(<<<'SQL'
                CREATE UNIQUE INDEX UNIQ_FD12B83DEA750E8 ON taxonomy (label)
            SQL);
        $this->addSql(<<<'SQL'
                CREATE INDEX IDX_FD12B83D727ACA70 ON taxonomy (parent_id)
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE taxonomy ADD CONSTRAINT FK_FD12B83D727ACA70 FOREIGN KEY (parent_id) REFERENCES taxonomy (id) NOT DEFERRABLE INITIALLY IMMEDIATE
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE taxonomy DROP CONSTRAINT FK_FD12B83D727ACA70
            SQL);
        $this->addSql(<<<'SQL'
                DROP TABLE taxonomy
            SQL);
    }
}
