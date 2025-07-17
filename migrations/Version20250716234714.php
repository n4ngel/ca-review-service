<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250716234714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, review_id VARCHAR(50) NOT NULL, status VARCHAR(20) NOT NULL, platform VARCHAR(50) NOT NULL, hotel_id VARCHAR(50) NOT NULL, guest_name VARCHAR(100) NOT NULL, submitted_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', content LONGTEXT DEFAULT NULL, rating SMALLINT NOT NULL, language VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_794381C63E2E969B (review_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review_response (id INT AUTO_INCREMENT NOT NULL, review_id INT NOT NULL, response_id VARCHAR(50) NOT NULL, responder VARCHAR(100) NOT NULL, replied_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', content LONGTEXT NOT NULL, INDEX IDX_1D3982F03E2E969B (review_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE review_response ADD CONSTRAINT FK_1D3982F03E2E969B FOREIGN KEY (review_id) REFERENCES review (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review_response DROP FOREIGN KEY FK_1D3982F03E2E969B');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE review_response');
    }
}
