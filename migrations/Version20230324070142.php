<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230324070142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE import_type ADD source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE import_type ADD CONSTRAINT FK_61F2B9A9953C1C61 FOREIGN KEY (source_id) REFERENCES source (id)');
        $this->addSql('CREATE INDEX IDX_61F2B9A9953C1C61 ON import_type (source_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE import_type DROP FOREIGN KEY FK_61F2B9A9953C1C61');
        $this->addSql('DROP INDEX IDX_61F2B9A9953C1C61 ON import_type');
        $this->addSql('ALTER TABLE import_type DROP source_id');
    }
}
