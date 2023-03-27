<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230326121122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier ADD session_id INT DEFAULT NULL, ADD source_id INT DEFAULT NULL, DROP source, DROP session');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551F613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551F953C1C61 FOREIGN KEY (source_id) REFERENCES source (id)');
        $this->addSql('CREATE INDEX IDX_9B76551F613FECDF ON fichier (session_id)');
        $this->addSql('CREATE INDEX IDX_9B76551F953C1C61 ON fichier (source_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551F613FECDF');
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551F953C1C61');
        $this->addSql('DROP INDEX IDX_9B76551F613FECDF ON fichier');
        $this->addSql('DROP INDEX IDX_9B76551F953C1C61 ON fichier');
        $this->addSql('ALTER TABLE fichier ADD source VARCHAR(255) NOT NULL, ADD session VARCHAR(255) NOT NULL, DROP session_id, DROP source_id');
    }
}
