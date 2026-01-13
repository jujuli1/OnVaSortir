<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260113075853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, sortie_id INT NOT NULL, INDEX IDX_5E90F6D6FB88E14F (utilisateur_id), INDEX IDX_5E90F6D6CC72D953 (sortie_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sortie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, date_heure DATETIME NOT NULL, lieu VARCHAR(255) DEFAULT NULL, utilisateur_id INT NOT NULL, INDEX IDX_3C3FD3F2FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6CC72D953 FOREIGN KEY (sortie_id) REFERENCES sortie (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE campus ADD CONSTRAINT FK_9D096811A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6FB88E14F');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6CC72D953');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2FB88E14F');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE sortie');
        $this->addSql('ALTER TABLE campus DROP FOREIGN KEY FK_9D096811A73F0036');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3AF5D55E1');
    }
}
