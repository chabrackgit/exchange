<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230416212528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE entite (id INT AUTO_INCREMENT NOT NULL, code_kyriba VARCHAR(255) NOT NULL, code_people_soft VARCHAR(255) NOT NULL, code_ubw VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etablissement (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fichier (id INT AUTO_INCREMENT NOT NULL, session_id INT DEFAULT NULL, entite_id INT DEFAULT NULL, template_code_id INT DEFAULT NULL, etablissement_id INT DEFAULT NULL, type_transfert_id INT DEFAULT NULL, user_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, nom_kyriba VARCHAR(255) NOT NULL, etat VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', customer VARCHAR(255) NOT NULL, nc_version VARCHAR(255) NOT NULL, uid VARCHAR(255) NOT NULL, other VARCHAR(255) DEFAULT NULL, mime_type VARCHAR(255) NOT NULL, INDEX IDX_9B76551F613FECDF (session_id), INDEX IDX_9B76551F9BEA957A (entite_id), INDEX IDX_9B76551F7462852E (template_code_id), INDEX IDX_9B76551FFF631228 (etablissement_id), INDEX IDX_9B76551FBBDFF1A8 (type_transfert_id), INDEX IDX_9B76551FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE template_code (id INT AUTO_INCREMENT NOT NULL, session_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, libelle VARCHAR(255) NOT NULL, dossier VARCHAR(255) NOT NULL, INDEX IDX_A2DB1AF5613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_transfert (id INT AUTO_INCREMENT NOT NULL, session_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_AA88EDAB613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, useruid VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551F613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551F9BEA957A FOREIGN KEY (entite_id) REFERENCES entite (id)');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551F7462852E FOREIGN KEY (template_code_id) REFERENCES template_code (id)');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551FFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551FBBDFF1A8 FOREIGN KEY (type_transfert_id) REFERENCES type_transfert (id)');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE template_code ADD CONSTRAINT FK_A2DB1AF5613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE type_transfert ADD CONSTRAINT FK_AA88EDAB613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551F613FECDF');
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551F9BEA957A');
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551F7462852E');
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551FFF631228');
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551FBBDFF1A8');
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551FA76ED395');
        $this->addSql('ALTER TABLE template_code DROP FOREIGN KEY FK_A2DB1AF5613FECDF');
        $this->addSql('ALTER TABLE type_transfert DROP FOREIGN KEY FK_AA88EDAB613FECDF');
        $this->addSql('DROP TABLE entite');
        $this->addSql('DROP TABLE etablissement');
        $this->addSql('DROP TABLE fichier');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE template_code');
        $this->addSql('DROP TABLE type_transfert');
        $this->addSql('DROP TABLE user');
    }
}
