<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240404144015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transportation_means (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(100) NOT NULL, capacity INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_ECB38BFC98260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE warehouse ADD CONSTRAINT FK_ECB38BFC98260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE coursier ADD transport_mean_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coursier ADD CONSTRAINT FK_6481BCF5D486EAC FOREIGN KEY (transport_mean_id) REFERENCES transportation_means (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6481BCF5D486EAC ON coursier (transport_mean_id)');
        $this->addSql('ALTER TABLE livraison_history ADD event VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE statut_coursier ADD region_id INT DEFAULT NULL, DROP region');
        $this->addSql('ALTER TABLE statut_coursier ADD CONSTRAINT FK_794EA76E98260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('CREATE INDEX IDX_794EA76E98260155 ON statut_coursier (region_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statut_coursier DROP FOREIGN KEY FK_794EA76E98260155');
        $this->addSql('ALTER TABLE coursier DROP FOREIGN KEY FK_6481BCF5D486EAC');
        $this->addSql('ALTER TABLE warehouse DROP FOREIGN KEY FK_ECB38BFC98260155');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE transportation_means');
        $this->addSql('DROP TABLE warehouse');
        $this->addSql('DROP INDEX UNIQ_6481BCF5D486EAC ON coursier');
        $this->addSql('ALTER TABLE coursier DROP transport_mean_id');
        $this->addSql('ALTER TABLE livraison_history DROP event');
        $this->addSql('DROP INDEX IDX_794EA76E98260155 ON statut_coursier');
        $this->addSql('ALTER TABLE statut_coursier ADD region VARCHAR(255) NOT NULL, DROP region_id');
    }
}
