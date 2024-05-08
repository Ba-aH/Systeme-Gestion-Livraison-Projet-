<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240508110017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE raison_signalement (id INT AUTO_INCREMENT NOT NULL, raison VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE statut_livraison ADD raison_signalement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE statut_livraison ADD CONSTRAINT FK_7A20ADD93849F42 FOREIGN KEY (raison_signalement_id) REFERENCES raison_signalement (id)');
        $this->addSql('CREATE INDEX IDX_7A20ADD93849F42 ON statut_livraison (raison_signalement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statut_livraison DROP FOREIGN KEY FK_7A20ADD93849F42');
        $this->addSql('DROP TABLE raison_signalement');
        $this->addSql('DROP INDEX IDX_7A20ADD93849F42 ON statut_livraison');
        $this->addSql('ALTER TABLE statut_livraison DROP raison_signalement_id');
    }
}
