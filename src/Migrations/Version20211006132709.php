<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211006132709 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add postcode column to review table';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE review ADD postcode INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C66339A411 FOREIGN KEY (postcode) REFERENCES postcode (id)');
        $this->addSql('CREATE INDEX IDX_794381C66339A411 ON review (postcode)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C66339A411');
        $this->addSql('DROP INDEX IDX_794381C66339A411 ON review');
        $this->addSql('ALTER TABLE review DROP postcode');
    }
}
