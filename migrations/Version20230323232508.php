<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230323232508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier ADD customer VARCHAR(255) NOT NULL, ADD nc_version VARCHAR(255) NOT NULL, ADD session VARCHAR(255) NOT NULL, ADD uid VARCHAR(255) NOT NULL, ADD export_type VARCHAR(255) NOT NULL, ADD template VARCHAR(255) NOT NULL, ADD other VARCHAR(255) DEFAULT NULL, ADD mime_type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier DROP customer, DROP nc_version, DROP session, DROP uid, DROP export_type, DROP template, DROP other, DROP mime_type');
    }
}
