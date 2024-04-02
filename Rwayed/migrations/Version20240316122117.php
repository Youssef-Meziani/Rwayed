<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240316122117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pneu DROP FOREIGN KEY FK_30D783FBED5E705E');
        $this->addSql('DROP TABLE caracteristique');
        $this->addSql('DROP INDEX IDX_30D783FBED5E705E ON pneu');
        $this->addSql('ALTER TABLE pneu ADD taille VARCHAR(50) NOT NULL, ADD indice_vitesse VARCHAR(10) NOT NULL, CHANGE prix_unitaire prix_unitaire DOUBLE PRECISION NOT NULL, CHANGE id_cara indice_charge INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE caracteristique (id INT AUTO_INCREMENT NOT NULL, taille VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, indice_charge INT NOT NULL, indice_vitesse VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE pneu DROP taille, DROP indice_vitesse, CHANGE prix_unitaire prix_unitaire DOUBLE PRECISION DEFAULT NULL, CHANGE indice_charge id_cara INT NOT NULL');
        $this->addSql('ALTER TABLE pneu ADD CONSTRAINT FK_30D783FBED5E705E FOREIGN KEY (id_cara) REFERENCES caracteristique (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_30D783FBED5E705E ON pneu (id_cara)');
    }
}
