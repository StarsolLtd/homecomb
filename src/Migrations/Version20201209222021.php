<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201209222021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change branch unique index';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_BB861B1F5E237E06 ON branch');
        $this->addSql('CREATE UNIQUE INDEX branch_unique ON branch (agency_id, name)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX branch_unique ON branch');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BB861B1F5E237E06 ON branch (name)');
    }
}
