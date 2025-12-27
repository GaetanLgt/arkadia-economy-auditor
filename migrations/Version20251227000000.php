<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251227000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create economy_snapshots table for ARK audit data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE economy_snapshots (
            id INT AUTO_INCREMENT NOT NULL,
            audit_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            server_id VARCHAR(255) NOT NULL,
            total_players INT NOT NULL,
            total_dinos INT NOT NULL,
            gini_coefficient NUMERIC(10, 4) NOT NULL,
            average_inflation NUMERIC(10, 2) NOT NULL,
            raw_data JSON NOT NULL,
            INDEX idx_audit_date (audit_date),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE economy_snapshots');
    }
}
