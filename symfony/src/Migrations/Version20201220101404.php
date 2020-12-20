<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201220101404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create review_solicitation table';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE review_solicitation (id INT AUTO_INCREMENT NOT NULL, sender_user_id INT NOT NULL, branch_id INT NOT NULL, property_id INT NOT NULL, review_id INT DEFAULT NULL, recipient_title VARCHAR(255) DEFAULT NULL, recipient_first_name VARCHAR(255) NOT NULL, recipient_last_name VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_16649C782A98155E (sender_user_id), INDEX IDX_16649C78DCD6CC49 (branch_id), INDEX IDX_16649C78549213EC (property_id), UNIQUE INDEX UNIQ_16649C783E2E969B (review_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE review_solicitation ADD CONSTRAINT FK_16649C782A98155E FOREIGN KEY (sender_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review_solicitation ADD CONSTRAINT FK_16649C78DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE review_solicitation ADD CONSTRAINT FK_16649C78549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE review_solicitation ADD CONSTRAINT FK_16649C783E2E969B FOREIGN KEY (review_id) REFERENCES review (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE review_solicitation');
    }
}
