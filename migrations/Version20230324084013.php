<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230324084013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE template_code ADD source_template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE template_code ADD CONSTRAINT FK_A2DB1AF539A55F18 FOREIGN KEY (source_template_id) REFERENCES source (id)');
        $this->addSql('CREATE INDEX IDX_A2DB1AF539A55F18 ON template_code (source_template_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE template_code DROP FOREIGN KEY FK_A2DB1AF539A55F18');
        $this->addSql('DROP INDEX IDX_A2DB1AF539A55F18 ON template_code');
        $this->addSql('ALTER TABLE template_code DROP source_template_id');
    }
}
