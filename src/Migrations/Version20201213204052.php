<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201213204052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'postcode_locale removal';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE postcode_locale');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE postcode_locale (postcode_id INT NOT NULL, locale_id INT NOT NULL, INDEX IDX_7E356783E559DFD1 (locale_id), INDEX IDX_7E356783EECBFDF1 (postcode_id), PRIMARY KEY(postcode_id, locale_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE postcode_locale ADD CONSTRAINT FK_7E356783E559DFD1 FOREIGN KEY (locale_id) REFERENCES locale (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE postcode_locale ADD CONSTRAINT FK_7E356783EECBFDF1 FOREIGN KEY (postcode_id) REFERENCES postcode (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
