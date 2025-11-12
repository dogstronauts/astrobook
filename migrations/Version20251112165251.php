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

final class Version20251112165251 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE password_reset_request ALTER password TYPE VARCHAR(128)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE password_reset_request ALTER password TYPE VARCHAR(255)');
    }
}
