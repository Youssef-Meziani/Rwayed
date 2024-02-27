<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227151537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE caracteristique (id_cara INT AUTO_INCREMENT NOT NULL, taille VARCHAR(50) NOT NULL, indice_charge INT NOT NULL, indice_vitesse VARCHAR(10) NOT NULL, PRIMARY KEY(id_cara)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pneu (id_pneu INT AUTO_INCREMENT NOT NULL, id_cara INT NOT NULL, marque VARCHAR(70) NOT NULL, type_vehicule VARCHAR(50) NOT NULL, slug VARCHAR(128) NOT NULL, saison VARCHAR(20) NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL, quantite_stock INT NOT NULL, description LONGTEXT NOT NULL, date_ajout DATE NOT NULL, UNIQUE INDEX UNIQ_30D783FB989D9B62 (slug), INDEX IDX_30D783FBED5E705E (id_cara), PRIMARY KEY(id_pneu)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pneu ADD CONSTRAINT FK_30D783FBED5E705E FOREIGN KEY (id_cara) REFERENCES caracteristique (id_cara)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pneu DROP FOREIGN KEY FK_30D783FBED5E705E');
        $this->addSql('DROP TABLE caracteristique');
        $this->addSql('DROP TABLE pneu');
    }
}
