<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211006131020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop FK in review table.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C629E42146');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C629E42146 FOREIGN KEY (related_entity_id) REFERENCES locale (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
