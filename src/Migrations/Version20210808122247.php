<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210808122247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add LocaleReview to TenancyReview relationship';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE review ADD tenancy_review_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C69D5A20C9 FOREIGN KEY (tenancy_review_id) REFERENCES tenancy_review (id)');
        $this->addSql('CREATE INDEX IDX_794381C69D5A20C9 ON review (tenancy_review_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C69D5A20C9');
        $this->addSql('DROP INDEX IDX_794381C69D5A20C9 ON review');
        $this->addSql('ALTER TABLE review DROP tenancy_review_id');
    }
}
