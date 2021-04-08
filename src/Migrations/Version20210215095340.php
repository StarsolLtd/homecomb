<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210215095340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create email table';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE email (id INT AUTO_INCREMENT NOT NULL, sender_user_id INT DEFAULT NULL, recipient_user_id INT DEFAULT NULL, resend_of_email_id INT DEFAULT NULL, type INT DEFAULT NULL, `from` VARCHAR(255) NOT NULL, `to` VARCHAR(255) NOT NULL, subject TEXT NOT NULL, html TEXT DEFAULT NULL, text TEXT NOT NULL, sent_at DATE DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E7927C742A98155E (sender_user_id), INDEX IDX_E7927C74B15EFB97 (recipient_user_id), UNIQUE INDEX UNIQ_E7927C7413365E55 (resend_of_email_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C742A98155E FOREIGN KEY (sender_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C74B15EFB97 FOREIGN KEY (recipient_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C7413365E55 FOREIGN KEY (resend_of_email_id) REFERENCES email (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE email DROP FOREIGN KEY FK_E7927C7413365E55');
        $this->addSql('DROP TABLE email');
    }
}
