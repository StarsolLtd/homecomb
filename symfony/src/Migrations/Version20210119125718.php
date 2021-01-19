<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210119125718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Misc migrations';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agency CHANGE slug slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE branch CHANGE slug slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE INDEX IDX_D1F4EB9A81257D5D ON flag (entity_id)');
        $this->addSql('ALTER TABLE property CHANGE slug slug VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agency CHANGE slug slug VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE branch CHANGE slug slug VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX IDX_D1F4EB9A81257D5D ON flag');
        $this->addSql('ALTER TABLE property CHANGE slug slug VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
