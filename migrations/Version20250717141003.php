<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250717141003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
        'ALTER TABLE review
                ADD INDEX idx_review_status_submitted_at  (status, submitted_at),
                ADD INDEX idx_review_status_hotel_submitted_at (status, hotel_id, submitted_at);'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review DROP INDEX idx_review_status_submitted_at');
        $this->addSql('ALTER TABLE review DROP INDEX idx_review_status_hotel_submitted_at');
    }
}
