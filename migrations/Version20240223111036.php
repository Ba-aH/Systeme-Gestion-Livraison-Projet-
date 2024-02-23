<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240223111036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrateur (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE adresse (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, formatted_address VARCHAR(255) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, ville VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(255) DEFAULT NULL, region VARCHAR(255) DEFAULT NULL, INDEX IDX_C35F081619EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, date_ajout DATETIME NOT NULL, date_modification DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE colis (id INT AUTO_INCREMENT NOT NULL, livraison_id INT DEFAULT NULL, nom_produit VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, poid DOUBLE PRECISION NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_470BDFF98E54FB25 (livraison_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coursier (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coursier_position_history (id INT AUTO_INCREMENT NOT NULL, coursier_id INT DEFAULT NULL, position_history_lat DOUBLE PRECISION DEFAULT NULL, position_history_long DOUBLE PRECISION DEFAULT NULL, position_date_ajout DATETIME NOT NULL, INDEX IDX_7D8E60AA2D831673 (coursier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livraison (id INT AUTO_INCREMENT NOT NULL, tourner_id INT DEFAULT NULL, code_pin INT DEFAULT NULL, livraison_date DATETIME DEFAULT NULL, frais_livraison DOUBLE PRECISION NOT NULL, prix_totale_livraison DOUBLE PRECISION NOT NULL, reference VARCHAR(255) NOT NULL, poid_livraison DOUBLE PRECISION DEFAULT NULL, INDEX IDX_A60C9F1F3BAFBF35 (tourner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statut_coursier (id INT AUTO_INCREMENT NOT NULL, coursier_id INT DEFAULT NULL, region VARCHAR(255) NOT NULL, debut_tourner DATETIME DEFAULT NULL, fin_tourner DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_794EA76E2D831673 (coursier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statut_livraison (id INT AUTO_INCREMENT NOT NULL, livraison_id INT DEFAULT NULL, status_title VARCHAR(255) NOT NULL, status_class VARCHAR(255) DEFAULT NULL, status_date_ajout DATETIME NOT NULL, status_date_modifier DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_7A20ADD8E54FB25 (livraison_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tourner (id INT AUTO_INCREMENT NOT NULL, coursier_id INT DEFAULT NULL, nb_livraison INT NOT NULL, poid_tourner DOUBLE PRECISION DEFAULT NULL, prix_tourner DOUBLE PRECISION NOT NULL, INDEX IDX_6825F8B92D831673 (coursier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F081619EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE colis ADD CONSTRAINT FK_470BDFF98E54FB25 FOREIGN KEY (livraison_id) REFERENCES livraison (id)');
        $this->addSql('ALTER TABLE coursier_position_history ADD CONSTRAINT FK_7D8E60AA2D831673 FOREIGN KEY (coursier_id) REFERENCES coursier (id)');
        $this->addSql('ALTER TABLE livraison ADD CONSTRAINT FK_A60C9F1F3BAFBF35 FOREIGN KEY (tourner_id) REFERENCES tourner (id)');
        $this->addSql('ALTER TABLE statut_coursier ADD CONSTRAINT FK_794EA76E2D831673 FOREIGN KEY (coursier_id) REFERENCES coursier (id)');
        $this->addSql('ALTER TABLE statut_livraison ADD CONSTRAINT FK_7A20ADD8E54FB25 FOREIGN KEY (livraison_id) REFERENCES livraison (id)');
        $this->addSql('ALTER TABLE tourner ADD CONSTRAINT FK_6825F8B92D831673 FOREIGN KEY (coursier_id) REFERENCES coursier (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse DROP FOREIGN KEY FK_C35F081619EB6921');
        $this->addSql('ALTER TABLE colis DROP FOREIGN KEY FK_470BDFF98E54FB25');
        $this->addSql('ALTER TABLE coursier_position_history DROP FOREIGN KEY FK_7D8E60AA2D831673');
        $this->addSql('ALTER TABLE livraison DROP FOREIGN KEY FK_A60C9F1F3BAFBF35');
        $this->addSql('ALTER TABLE statut_coursier DROP FOREIGN KEY FK_794EA76E2D831673');
        $this->addSql('ALTER TABLE statut_livraison DROP FOREIGN KEY FK_7A20ADD8E54FB25');
        $this->addSql('ALTER TABLE tourner DROP FOREIGN KEY FK_6825F8B92D831673');
        $this->addSql('DROP TABLE administrateur');
        $this->addSql('DROP TABLE adresse');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE colis');
        $this->addSql('DROP TABLE coursier');
        $this->addSql('DROP TABLE coursier_position_history');
        $this->addSql('DROP TABLE livraison');
        $this->addSql('DROP TABLE statut_coursier');
        $this->addSql('DROP TABLE statut_livraison');
        $this->addSql('DROP TABLE tourner');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
