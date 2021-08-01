<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210801185311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tables';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE agency (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, postcode VARCHAR(255) DEFAULT NULL, country_code VARCHAR(255) DEFAULT NULL, external_url VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, published TINYINT(1) DEFAULT \'0\' NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_70C0C6E65E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, response_id INT NOT NULL, content TEXT DEFAULT NULL, rating INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_DADD4A251E27F6BF (question_id), INDEX IDX_DADD4A25FBF32840 (response_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE answer_choice (answer_id INT NOT NULL, choice_id INT NOT NULL, INDEX IDX_33526035AA334807 (answer_id), INDEX IDX_33526035998666D1 (choice_id), PRIMARY KEY(answer_id, choice_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE branch (id INT AUTO_INCREMENT NOT NULL, agency_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, telephone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, published TINYINT(1) DEFAULT \'0\' NOT NULL, slug VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_BB861B1FCDEADB2A (agency_id), UNIQUE INDEX branch_unique (agency_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE choice (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, name VARCHAR(255) NOT NULL, help TEXT DEFAULT NULL, sort_order INT DEFAULT 100 NOT NULL, published TINYINT(1) DEFAULT \'1\' NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_C1AB5A921E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, related_entity_id INT NOT NULL, user_id INT DEFAULT NULL, content TEXT NOT NULL, published TINYINT(1) DEFAULT \'0\' NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, related_entity_name VARCHAR(255) NOT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526C29E42146 (related_entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email (id INT AUTO_INCREMENT NOT NULL, sender_user_id INT DEFAULT NULL, recipient_user_id INT DEFAULT NULL, resend_of_email_id INT DEFAULT NULL, sender VARCHAR(255) NOT NULL, recipient VARCHAR(255) NOT NULL, subject TEXT NOT NULL, text TEXT NOT NULL, html TEXT DEFAULT NULL, type INT DEFAULT NULL, sent_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E7927C742A98155E (sender_user_id), INDEX IDX_E7927C74B15EFB97 (recipient_user_id), UNIQUE INDEX UNIQ_E7927C7413365E55 (resend_of_email_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flag (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, entity_id INT NOT NULL, content TEXT DEFAULT NULL, valid TINYINT(1) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, entity_name VARCHAR(255) NOT NULL, INDEX IDX_D1F4EB9AA76ED395 (user_id), INDEX IDX_D1F4EB9A81257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, agency_id INT DEFAULT NULL, branch_id INT DEFAULT NULL, locale_id INT DEFAULT NULL, tenancy_review_id INT DEFAULT NULL, user_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, image VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_C53D045FCDEADB2A (agency_id), INDEX IDX_C53D045FDCD6CC49 (branch_id), INDEX IDX_C53D045FE559DFD1 (locale_id), INDEX IDX_C53D045F9D5A20C9 (tenancy_review_id), INDEX IDX_C53D045FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interaction (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, entity_id INT NOT NULL, session_id VARCHAR(255) DEFAULT NULL, ip_address VARCHAR(255) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, entity_name VARCHAR(255) NOT NULL, INDEX IDX_378DFDA7A76ED395 (user_id), INDEX IDX_378DFDA781257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE locale (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, slug VARCHAR(255) NOT NULL, published TINYINT(1) DEFAULT \'1\' NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_4180C6985E237E06 (name), UNIQUE INDEX UNIQ_4180C698989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE locale_postcode (locale_id INT NOT NULL, postcode_id INT NOT NULL, INDEX IDX_CC6F7CDCE559DFD1 (locale_id), INDEX IDX_CC6F7CDCEECBFDF1 (postcode_id), PRIMARY KEY(locale_id, postcode_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE locale_tenancy_review (locale_id INT NOT NULL, tenancy_review_id INT NOT NULL, INDEX IDX_4DEF13C9E559DFD1 (locale_id), INDEX IDX_4DEF13C99D5A20C9 (tenancy_review_id), PRIMARY KEY(locale_id, tenancy_review_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE locale_related (locale_id INT NOT NULL, related_locale_id INT NOT NULL, INDEX IDX_3C94F139E559DFD1 (locale_id), INDEX IDX_3C94F1394D9883CA (related_locale_id), PRIMARY KEY(locale_id, related_locale_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE postcode (id INT AUTO_INCREMENT NOT NULL, postcode VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6339A4116339A411 (postcode), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, address_line1 VARCHAR(255) NOT NULL, address_line2 VARCHAR(255) DEFAULT NULL, address_line3 VARCHAR(255) DEFAULT NULL, address_line4 VARCHAR(255) DEFAULT NULL, locality VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, county VARCHAR(255) DEFAULT NULL, postcode VARCHAR(255) NOT NULL, country_code VARCHAR(255) NOT NULL, latitude NUMERIC(10, 8) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, vendor_property_id VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, published TINYINT(1) DEFAULT \'1\' NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, survey_id INT NOT NULL, type VARCHAR(255) NOT NULL, content TEXT NOT NULL, help TEXT DEFAULT NULL, high_meaning VARCHAR(255) DEFAULT NULL, low_meaning VARCHAR(255) DEFAULT NULL, published TINYINT(1) DEFAULT \'1\' NOT NULL, sort_order INT DEFAULT 100 NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B6F7494EB3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE response (id INT AUTO_INCREMENT NOT NULL, survey_id INT NOT NULL, user_id INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3E7B0BFBB3FE509D (survey_id), INDEX IDX_3E7B0BFBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, slug VARCHAR(255) NOT NULL, published TINYINT(1) DEFAULT \'1\' NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_AD5F9BFC989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tenancy_review (id INT AUTO_INCREMENT NOT NULL, branch_id INT DEFAULT NULL, property_id INT NOT NULL, user_id INT DEFAULT NULL, author VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, content TEXT DEFAULT NULL, overall_stars INT DEFAULT NULL, property_stars INT DEFAULT NULL, agency_stars INT DEFAULT NULL, landlord_stars INT DEFAULT NULL, published TINYINT(1) DEFAULT \'0\' NOT NULL, start DATE DEFAULT NULL, end DATE DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_655E6018DCD6CC49 (branch_id), INDEX IDX_655E6018549213EC (property_id), INDEX IDX_655E6018A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tenancy_review_solicitation (id INT AUTO_INCREMENT NOT NULL, sender_user_id INT NOT NULL, branch_id INT NOT NULL, property_id INT NOT NULL, tenancy_review_id INT DEFAULT NULL, recipient_title VARCHAR(255) DEFAULT NULL, recipient_first_name VARCHAR(255) NOT NULL, recipient_last_name VARCHAR(255) NOT NULL, recipient_email VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_CEA225832A98155E (sender_user_id), INDEX IDX_CEA22583DCD6CC49 (branch_id), INDEX IDX_CEA22583549213EC (property_id), UNIQUE INDEX UNIQ_CEA225839D5A20C9 (tenancy_review_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, admin_agency_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, title VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649241DDE18 (admin_agency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, entity_id INT NOT NULL, positive TINYINT(1) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, entity_name VARCHAR(255) NOT NULL, INDEX IDX_5A108564A76ED395 (user_id), INDEX IDX_5A10856481257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25FBF32840 FOREIGN KEY (response_id) REFERENCES response (id)');
        $this->addSql('ALTER TABLE answer_choice ADD CONSTRAINT FK_33526035AA334807 FOREIGN KEY (answer_id) REFERENCES answer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE answer_choice ADD CONSTRAINT FK_33526035998666D1 FOREIGN KEY (choice_id) REFERENCES choice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE branch ADD CONSTRAINT FK_BB861B1FCDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id)');
        $this->addSql('ALTER TABLE choice ADD CONSTRAINT FK_C1AB5A921E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C29E42146 FOREIGN KEY (related_entity_id) REFERENCES tenancy_review (id)');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C742A98155E FOREIGN KEY (sender_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C74B15EFB97 FOREIGN KEY (recipient_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C7413365E55 FOREIGN KEY (resend_of_email_id) REFERENCES email (id)');
        $this->addSql('ALTER TABLE flag ADD CONSTRAINT FK_D1F4EB9AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FCDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FDCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FE559DFD1 FOREIGN KEY (locale_id) REFERENCES locale (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F9D5A20C9 FOREIGN KEY (tenancy_review_id) REFERENCES tenancy_review (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE interaction ADD CONSTRAINT FK_378DFDA7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE locale_postcode ADD CONSTRAINT FK_CC6F7CDCE559DFD1 FOREIGN KEY (locale_id) REFERENCES locale (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE locale_postcode ADD CONSTRAINT FK_CC6F7CDCEECBFDF1 FOREIGN KEY (postcode_id) REFERENCES postcode (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE locale_tenancy_review ADD CONSTRAINT FK_4DEF13C9E559DFD1 FOREIGN KEY (locale_id) REFERENCES locale (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE locale_tenancy_review ADD CONSTRAINT FK_4DEF13C99D5A20C9 FOREIGN KEY (tenancy_review_id) REFERENCES tenancy_review (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE locale_related ADD CONSTRAINT FK_3C94F139E559DFD1 FOREIGN KEY (locale_id) REFERENCES locale (id)');
        $this->addSql('ALTER TABLE locale_related ADD CONSTRAINT FK_3C94F1394D9883CA FOREIGN KEY (related_locale_id) REFERENCES locale (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EB3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFBB3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tenancy_review ADD CONSTRAINT FK_655E6018DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE tenancy_review ADD CONSTRAINT FK_655E6018549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE tenancy_review ADD CONSTRAINT FK_655E6018A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tenancy_review_solicitation ADD CONSTRAINT FK_CEA225832A98155E FOREIGN KEY (sender_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tenancy_review_solicitation ADD CONSTRAINT FK_CEA22583DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE tenancy_review_solicitation ADD CONSTRAINT FK_CEA22583549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE tenancy_review_solicitation ADD CONSTRAINT FK_CEA225839D5A20C9 FOREIGN KEY (tenancy_review_id) REFERENCES tenancy_review (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649241DDE18 FOREIGN KEY (admin_agency_id) REFERENCES agency (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE branch DROP FOREIGN KEY FK_BB861B1FCDEADB2A');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FCDEADB2A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649241DDE18');
        $this->addSql('ALTER TABLE answer_choice DROP FOREIGN KEY FK_33526035AA334807');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FDCD6CC49');
        $this->addSql('ALTER TABLE tenancy_review DROP FOREIGN KEY FK_655E6018DCD6CC49');
        $this->addSql('ALTER TABLE tenancy_review_solicitation DROP FOREIGN KEY FK_CEA22583DCD6CC49');
        $this->addSql('ALTER TABLE answer_choice DROP FOREIGN KEY FK_33526035998666D1');
        $this->addSql('ALTER TABLE email DROP FOREIGN KEY FK_E7927C7413365E55');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FE559DFD1');
        $this->addSql('ALTER TABLE locale_postcode DROP FOREIGN KEY FK_CC6F7CDCE559DFD1');
        $this->addSql('ALTER TABLE locale_tenancy_review DROP FOREIGN KEY FK_4DEF13C9E559DFD1');
        $this->addSql('ALTER TABLE locale_related DROP FOREIGN KEY FK_3C94F139E559DFD1');
        $this->addSql('ALTER TABLE locale_related DROP FOREIGN KEY FK_3C94F1394D9883CA');
        $this->addSql('ALTER TABLE locale_postcode DROP FOREIGN KEY FK_CC6F7CDCEECBFDF1');
        $this->addSql('ALTER TABLE tenancy_review DROP FOREIGN KEY FK_655E6018549213EC');
        $this->addSql('ALTER TABLE tenancy_review_solicitation DROP FOREIGN KEY FK_CEA22583549213EC');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A251E27F6BF');
        $this->addSql('ALTER TABLE choice DROP FOREIGN KEY FK_C1AB5A921E27F6BF');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25FBF32840');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EB3FE509D');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFBB3FE509D');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C29E42146');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F9D5A20C9');
        $this->addSql('ALTER TABLE locale_tenancy_review DROP FOREIGN KEY FK_4DEF13C99D5A20C9');
        $this->addSql('ALTER TABLE tenancy_review_solicitation DROP FOREIGN KEY FK_CEA225839D5A20C9');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE email DROP FOREIGN KEY FK_E7927C742A98155E');
        $this->addSql('ALTER TABLE email DROP FOREIGN KEY FK_E7927C74B15EFB97');
        $this->addSql('ALTER TABLE flag DROP FOREIGN KEY FK_D1F4EB9AA76ED395');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FA76ED395');
        $this->addSql('ALTER TABLE interaction DROP FOREIGN KEY FK_378DFDA7A76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFBA76ED395');
        $this->addSql('ALTER TABLE tenancy_review DROP FOREIGN KEY FK_655E6018A76ED395');
        $this->addSql('ALTER TABLE tenancy_review_solicitation DROP FOREIGN KEY FK_CEA225832A98155E');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564A76ED395');
        $this->addSql('DROP TABLE agency');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE answer_choice');
        $this->addSql('DROP TABLE branch');
        $this->addSql('DROP TABLE choice');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE email');
        $this->addSql('DROP TABLE flag');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE interaction');
        $this->addSql('DROP TABLE locale');
        $this->addSql('DROP TABLE locale_postcode');
        $this->addSql('DROP TABLE locale_tenancy_review');
        $this->addSql('DROP TABLE locale_related');
        $this->addSql('DROP TABLE postcode');
        $this->addSql('DROP TABLE property');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE response');
        $this->addSql('DROP TABLE survey');
        $this->addSql('DROP TABLE tenancy_review');
        $this->addSql('DROP TABLE tenancy_review_solicitation');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vote');
    }
}
