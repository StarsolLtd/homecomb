<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201212114756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add locale_related table';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE locale_related (locale_id INT NOT NULL, related_locale_id INT NOT NULL, INDEX IDX_3C94F139E559DFD1 (locale_id), INDEX IDX_3C94F1394D9883CA (related_locale_id), PRIMARY KEY(locale_id, related_locale_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE locale_related ADD CONSTRAINT FK_3C94F139E559DFD1 FOREIGN KEY (locale_id) REFERENCES locale (id)');
        $this->addSql('ALTER TABLE locale_related ADD CONSTRAINT FK_3C94F1394D9883CA FOREIGN KEY (related_locale_id) REFERENCES locale (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE locale_related');
    }
}
