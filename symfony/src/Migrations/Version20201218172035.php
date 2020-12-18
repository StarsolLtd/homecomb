<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201218172035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add agency admin users';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agency ADD external_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD admin_agency_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649241DDE18 FOREIGN KEY (admin_agency_id) REFERENCES agency (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649241DDE18 ON user (admin_agency_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agency DROP external_url');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649241DDE18');
        $this->addSql('DROP INDEX IDX_8D93D649241DDE18 ON user');
        $this->addSql('ALTER TABLE user DROP admin_agency_id');
    }
}
