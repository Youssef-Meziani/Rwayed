<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240316165458 extends AbstractMigration
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
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418BBF79FF9');
        $this->addSql('ALTER TABLE photo ADD updated_at DATETIME DEFAULT NULL, CHANGE path path VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418BBF79FF9 FOREIGN KEY (id_pneu) REFERENCES pneu (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX IDX_30D783FBED5E705E ON pneu');
        $this->addSql('ALTER TABLE pneu ADD updated_at DATETIME DEFAULT NULL, ADD taille VARCHAR(50) NOT NULL, ADD indice_vitesse VARCHAR(10) NOT NULL, CHANGE id_cara indice_charge INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE caracteristique (id INT AUTO_INCREMENT NOT NULL, taille VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, indice_charge INT NOT NULL, indice_vitesse VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE pneu DROP updated_at, DROP taille, DROP indice_vitesse, CHANGE indice_charge id_cara INT NOT NULL');
        $this->addSql('ALTER TABLE pneu ADD CONSTRAINT FK_30D783FBED5E705E FOREIGN KEY (id_cara) REFERENCES caracteristique (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_30D783FBED5E705E ON pneu (id_cara)');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418BBF79FF9');
        $this->addSql('ALTER TABLE photo DROP updated_at, CHANGE path path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418BBF79FF9 FOREIGN KEY (id_pneu) REFERENCES pneu (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
