<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325224120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE template_code ADD session_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE template_code ADD CONSTRAINT FK_A2DB1AF5613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('CREATE INDEX IDX_A2DB1AF5613FECDF ON template_code (session_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE template_code DROP FOREIGN KEY FK_A2DB1AF5613FECDF');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP INDEX IDX_A2DB1AF5613FECDF ON template_code');
        $this->addSql('ALTER TABLE template_code DROP session_id');
    }
}
