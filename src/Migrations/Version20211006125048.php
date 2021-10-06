<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211006125048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create broadband provider table.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE broadband_provider (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, country_code VARCHAR(255) DEFAULT NULL, external_url VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, published TINYINT(1) DEFAULT \'0\' NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_B16832BA5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE broadband_provider');
    }
}
