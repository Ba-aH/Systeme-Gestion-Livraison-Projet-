<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240513191558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livraison_history ADD livraison_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE livraison_history ADD CONSTRAINT FK_674C1E298E54FB25 FOREIGN KEY (livraison_id) REFERENCES livraison (id)');
        $this->addSql('CREATE INDEX IDX_674C1E298E54FB25 ON livraison_history (livraison_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livraison_history DROP FOREIGN KEY FK_674C1E298E54FB25');
        $this->addSql('DROP INDEX IDX_674C1E298E54FB25 ON livraison_history');
        $this->addSql('ALTER TABLE livraison_history DROP livraison_id');
    }
}
