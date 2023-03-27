<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230324083332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE source DROP FOREIGN KEY FK_5F8A7F737462852E');
        $this->addSql('DROP INDEX IDX_5F8A7F737462852E ON source');
        $this->addSql('ALTER TABLE source ADD template_code VARCHAR(255) NOT NULL, DROP template_code_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE source ADD template_code_id INT DEFAULT NULL, DROP template_code');
        $this->addSql('ALTER TABLE source ADD CONSTRAINT FK_5F8A7F737462852E FOREIGN KEY (template_code_id) REFERENCES template_code (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5F8A7F737462852E ON source (template_code_id)');
    }
}
