<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230326150412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier ADD entite_id INT DEFAULT NULL, ADD template_code_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551F9BEA957A FOREIGN KEY (entite_id) REFERENCES entite (id)');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551F7462852E FOREIGN KEY (template_code_id) REFERENCES template_code (id)');
        $this->addSql('CREATE INDEX IDX_9B76551F9BEA957A ON fichier (entite_id)');
        $this->addSql('CREATE INDEX IDX_9B76551F7462852E ON fichier (template_code_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551F9BEA957A');
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551F7462852E');
        $this->addSql('DROP INDEX IDX_9B76551F9BEA957A ON fichier');
        $this->addSql('DROP INDEX IDX_9B76551F7462852E ON fichier');
        $this->addSql('ALTER TABLE fichier DROP entite_id, DROP template_code_id');
    }
}
