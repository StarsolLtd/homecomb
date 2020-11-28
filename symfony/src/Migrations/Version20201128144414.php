<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201128144414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add various different star ratings.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE review ADD property_stars INT DEFAULT NULL, ADD agency_stars INT DEFAULT NULL, ADD landlord_stars INT DEFAULT NULL, CHANGE stars overall_stars INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE review ADD stars INT DEFAULT NULL, DROP overall_stars, DROP property_stars, DROP agency_stars, DROP landlord_stars');
    }
}
