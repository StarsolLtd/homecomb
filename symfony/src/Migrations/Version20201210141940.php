<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201210141940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create locale_review many-to-many relationship';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE locale_review (locale_id INT NOT NULL, review_id INT NOT NULL, INDEX IDX_9A82C7DE559DFD1 (locale_id), INDEX IDX_9A82C7D3E2E969B (review_id), PRIMARY KEY(locale_id, review_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE locale_review ADD CONSTRAINT FK_9A82C7DE559DFD1 FOREIGN KEY (locale_id) REFERENCES locale (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE locale_review ADD CONSTRAINT FK_9A82C7D3E2E969B FOREIGN KEY (review_id) REFERENCES review (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE locale_review');
    }
}
