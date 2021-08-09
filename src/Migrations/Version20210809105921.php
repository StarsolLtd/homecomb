<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210809105921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create district table and relationships';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE district (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, county VARCHAR(255) DEFAULT NULL, country_code VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, published TINYINT(1) DEFAULT \'1\' NOT NULL, slug VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_31C15487989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE locale ADD district_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE locale ADD CONSTRAINT FK_4180C698B08FA272 FOREIGN KEY (district_id) REFERENCES district (id)');
        $this->addSql('CREATE INDEX IDX_4180C698B08FA272 ON locale (district_id)');
        $this->addSql('ALTER TABLE property ADD district_id INT DEFAULT NULL, CHANGE district address_district VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDEB08FA272 FOREIGN KEY (district_id) REFERENCES district (id)');
        $this->addSql('CREATE INDEX IDX_8BF21CDEB08FA272 ON property (district_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE locale DROP FOREIGN KEY FK_4180C698B08FA272');
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDEB08FA272');
        $this->addSql('DROP TABLE district');
        $this->addSql('DROP INDEX IDX_4180C698B08FA272 ON locale');
        $this->addSql('ALTER TABLE locale DROP district_id');
        $this->addSql('DROP INDEX IDX_8BF21CDEB08FA272 ON property');
        $this->addSql('ALTER TABLE property DROP district_id, CHANGE address_district district VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
