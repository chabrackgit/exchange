<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325233644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type_transfert ADD session_id INT DEFAULT NULL, DROP type');
        $this->addSql('ALTER TABLE type_transfert ADD CONSTRAINT FK_AA88EDAB613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('CREATE INDEX IDX_AA88EDAB613FECDF ON type_transfert (session_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type_transfert DROP FOREIGN KEY FK_AA88EDAB613FECDF');
        $this->addSql('DROP INDEX IDX_AA88EDAB613FECDF ON type_transfert');
        $this->addSql('ALTER TABLE type_transfert ADD type VARCHAR(255) NOT NULL, DROP session_id');
    }
}
