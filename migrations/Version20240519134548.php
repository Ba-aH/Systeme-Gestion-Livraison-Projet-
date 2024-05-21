<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240519134548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coursier ADD transport_mean_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coursier ADD CONSTRAINT FK_6481BCF5D486EAC FOREIGN KEY (transport_mean_id) REFERENCES transportation_means (id)');
        $this->addSql('CREATE INDEX IDX_6481BCF5D486EAC ON coursier (transport_mean_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coursier DROP FOREIGN KEY FK_6481BCF5D486EAC');
        $this->addSql('DROP INDEX IDX_6481BCF5D486EAC ON coursier');
        $this->addSql('ALTER TABLE coursier DROP transport_mean_id');
    }
}
