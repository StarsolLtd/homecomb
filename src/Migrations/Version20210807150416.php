<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210807150416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add locale type';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE locale ADD city_id INT DEFAULT NULL, ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE locale ADD CONSTRAINT FK_4180C6988BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_4180C6988BAC62AF ON locale (city_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE locale DROP FOREIGN KEY FK_4180C6988BAC62AF');
        $this->addSql('DROP INDEX IDX_4180C6988BAC62AF ON locale');
        $this->addSql('ALTER TABLE locale DROP city_id, DROP type');
    }
}
