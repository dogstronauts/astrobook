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

final class Version20250709193503 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE contact (id UUID NOT NULL, firstname VARCHAR(64) NOT NULL, lastname VARCHAR(64) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, address_data JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C62E638E7927C74 ON contact (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C62E638444F97DD ON contact (phone)');
        $this->addSql('CREATE TABLE user_contact (type VARCHAR(32) NOT NULL, user_id UUID NOT NULL, contact_id UUID NOT NULL, PRIMARY KEY(user_id, contact_id))');
        $this->addSql('CREATE INDEX IDX_146FF832A76ED395 ON user_contact (user_id)');
        $this->addSql('CREATE INDEX IDX_146FF832E7A1254A ON user_contact (contact_id)');
        $this->addSql('ALTER TABLE user_contact ADD CONSTRAINT FK_146FF832A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_contact ADD CONSTRAINT FK_146FF832E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_contact DROP CONSTRAINT FK_146FF832A76ED395');
        $this->addSql('ALTER TABLE user_contact DROP CONSTRAINT FK_146FF832E7A1254A');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE user_contact');
    }
}
