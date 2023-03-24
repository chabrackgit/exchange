<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230324052048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier_import ADD template_type VARCHAR(255) NOT NULL, ADD template_code VARCHAR(255) NOT NULL, ADD company VARCHAR(255) NOT NULL, ADD repository VARCHAR(255) NOT NULL, DROP branch, DROP format');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier_import ADD branch VARCHAR(255) NOT NULL, ADD format VARCHAR(255) NOT NULL, DROP template_type, DROP template_code, DROP company, DROP repository');
    }
}
