<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229114729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statut_coursier ADD coursier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE statut_coursier ADD CONSTRAINT FK_794EA76E2D831673 FOREIGN KEY (coursier_id) REFERENCES coursier (id)');
        $this->addSql('CREATE INDEX IDX_794EA76E2D831673 ON statut_coursier (coursier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statut_coursier DROP FOREIGN KEY FK_794EA76E2D831673');
        $this->addSql('DROP INDEX IDX_794EA76E2D831673 ON statut_coursier');
        $this->addSql('ALTER TABLE statut_coursier DROP coursier_id');
    }
}
