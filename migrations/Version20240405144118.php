<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240405144118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coursier_position_history ADD tourner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coursier_position_history ADD CONSTRAINT FK_7D8E60AA3BAFBF35 FOREIGN KEY (tourner_id) REFERENCES tourner (id)');
        $this->addSql('CREATE INDEX IDX_7D8E60AA3BAFBF35 ON coursier_position_history (tourner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coursier_position_history DROP FOREIGN KEY FK_7D8E60AA3BAFBF35');
        $this->addSql('DROP INDEX IDX_7D8E60AA3BAFBF35 ON coursier_position_history');
        $this->addSql('ALTER TABLE coursier_position_history DROP tourner_id');
    }
}
