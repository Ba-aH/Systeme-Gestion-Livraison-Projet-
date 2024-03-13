<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240312004702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE colis ADD statut VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE coursier ADD is_verified TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE livraison_history DROP FOREIGN KEY FK_674C1E292D831673');
        $this->addSql('DROP INDEX IDX_674C1E292D831673 ON livraison_history');
        $this->addSql('ALTER TABLE livraison_history DROP coursier_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE colis DROP statut');
        $this->addSql('ALTER TABLE coursier DROP is_verified');
        $this->addSql('ALTER TABLE livraison_history ADD coursier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE livraison_history ADD CONSTRAINT FK_674C1E292D831673 FOREIGN KEY (coursier_id) REFERENCES coursier (id)');
        $this->addSql('CREATE INDEX IDX_674C1E292D831673 ON livraison_history (coursier_id)');
    }
}
