<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201127202738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add branch/agency relationship';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE branch ADD agency_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE branch ADD CONSTRAINT FK_BB861B1FCDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id)');
        $this->addSql('CREATE INDEX IDX_BB861B1FCDEADB2A ON branch (agency_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE branch DROP FOREIGN KEY FK_BB861B1FCDEADB2A');
        $this->addSql('DROP INDEX IDX_BB861B1FCDEADB2A ON branch');
        $this->addSql('ALTER TABLE branch DROP agency_id');
    }
}
