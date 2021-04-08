<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201202170826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add published boolean to various tables';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agency ADD published TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE branch ADD published TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE property ADD published TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE review ADD published TINYINT(1) DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agency DROP published');
        $this->addSql('ALTER TABLE branch DROP published');
        $this->addSql('ALTER TABLE property DROP published');
        $this->addSql('ALTER TABLE review DROP published');
    }
}
