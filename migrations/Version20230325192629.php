<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325192629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier_import DROP FOREIGN KEY FK_B57B2F37953C1C61');
        $this->addSql('DROP INDEX IDX_B57B2F37953C1C61 ON fichier_import');
        $this->addSql('ALTER TABLE fichier_import DROP source_id');
        $this->addSql('ALTER TABLE type_transfert ADD type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type_transfert DROP type');
        $this->addSql('ALTER TABLE fichier_import ADD source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fichier_import ADD CONSTRAINT FK_B57B2F37953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_B57B2F37953C1C61 ON fichier_import (source_id)');
    }
}
