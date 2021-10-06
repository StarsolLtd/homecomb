<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211006134104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create broadband_provider_review table.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE broadband_provider_review (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, related_entity_id INT DEFAULT NULL, postcode INT DEFAULT NULL, author VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, content TEXT DEFAULT NULL, overall_stars INT DEFAULT NULL, published TINYINT(1) DEFAULT \'0\' NOT NULL, slug VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_414BC57E989D9B62 (slug), INDEX IDX_414BC57EA76ED395 (user_id), INDEX IDX_414BC57E29E42146 (related_entity_id), INDEX IDX_414BC57E6339A411 (postcode), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE broadband_provider_review ADD CONSTRAINT FK_414BC57EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE broadband_provider_review ADD CONSTRAINT FK_414BC57E29E42146 FOREIGN KEY (related_entity_id) REFERENCES broadband_provider (id)');
        $this->addSql('ALTER TABLE broadband_provider_review ADD CONSTRAINT FK_414BC57E6339A411 FOREIGN KEY (postcode) REFERENCES postcode (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE broadband_provider_review');
    }
}
