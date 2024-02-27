<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240226190245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE livraison_history (id INT AUTO_INCREMENT NOT NULL, livraison_id INT DEFAULT NULL, date_ajout DATE DEFAULT NULL, UNIQUE INDEX UNIQ_674C1E298E54FB25 (livraison_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE livraison_history ADD CONSTRAINT FK_674C1E298E54FB25 FOREIGN KEY (livraison_id) REFERENCES livraison (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livraison_history DROP FOREIGN KEY FK_674C1E298E54FB25');
        $this->addSql('DROP TABLE livraison_history');
    }
}
