<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240404142920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statut_coursier ADD region_id INT DEFAULT NULL, DROP region');
        $this->addSql('ALTER TABLE statut_coursier ADD CONSTRAINT FK_794EA76E98260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('CREATE INDEX IDX_794EA76E98260155 ON statut_coursier (region_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statut_coursier DROP FOREIGN KEY FK_794EA76E98260155');
        $this->addSql('DROP INDEX IDX_794EA76E98260155 ON statut_coursier');
        $this->addSql('ALTER TABLE statut_coursier ADD region VARCHAR(255) NOT NULL, DROP region_id');
    }
}
