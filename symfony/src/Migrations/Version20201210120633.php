<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201210120633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create postcode table and its relationship with locale';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE locale_postcode (locale_id INT NOT NULL, postcode_id INT NOT NULL, INDEX IDX_CC6F7CDCE559DFD1 (locale_id), INDEX IDX_CC6F7CDCEECBFDF1 (postcode_id), PRIMARY KEY(locale_id, postcode_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE postcode (id INT AUTO_INCREMENT NOT NULL, postcode VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6339A4116339A411 (postcode), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE postcode_locale (postcode_id INT NOT NULL, locale_id INT NOT NULL, INDEX IDX_7E356783EECBFDF1 (postcode_id), INDEX IDX_7E356783E559DFD1 (locale_id), PRIMARY KEY(postcode_id, locale_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE locale_postcode ADD CONSTRAINT FK_CC6F7CDCE559DFD1 FOREIGN KEY (locale_id) REFERENCES locale (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE locale_postcode ADD CONSTRAINT FK_CC6F7CDCEECBFDF1 FOREIGN KEY (postcode_id) REFERENCES postcode (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE postcode_locale ADD CONSTRAINT FK_7E356783EECBFDF1 FOREIGN KEY (postcode_id) REFERENCES postcode (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE postcode_locale ADD CONSTRAINT FK_7E356783E559DFD1 FOREIGN KEY (locale_id) REFERENCES locale (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE locale_postcode DROP FOREIGN KEY FK_CC6F7CDCEECBFDF1');
        $this->addSql('ALTER TABLE postcode_locale DROP FOREIGN KEY FK_7E356783EECBFDF1');
        $this->addSql('DROP TABLE locale_postcode');
        $this->addSql('DROP TABLE postcode');
        $this->addSql('DROP TABLE postcode_locale');
    }
}
