<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230505112351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD pdf_file VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation RENAME INDEX apartment_id TO IDX_42C84955176DFE85');
        $this->addSql('ALTER TABLE reservation RENAME INDEX state_id TO IDX_42C849555D83CC1');
        $this->addSql('ALTER TABLE reservation RENAME INDEX client_id TO IDX_42C8495519EB6921');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP pdf_file');
        $this->addSql('ALTER TABLE reservation RENAME INDEX idx_42c8495519eb6921 TO client_id');
        $this->addSql('ALTER TABLE reservation RENAME INDEX idx_42c84955176dfe85 TO apartment_id');
        $this->addSql('ALTER TABLE reservation RENAME INDEX idx_42c849555d83cc1 TO state_id');
    }
}
