<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240304085936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adherent (id BIGINT NOT NULL, points_fidelite INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `admin` (id BIGINT NOT NULL, rang VARCHAR(50) NOT NULL, is_super TINYINT(1) NOT NULL, date_embauche DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE caracteristique (id INT AUTO_INCREMENT NOT NULL, taille VARCHAR(50) NOT NULL, indice_charge INT NOT NULL, indice_vitesse VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personne (id BIGINT AUTO_INCREMENT NOT NULL, nom VARCHAR(20) NOT NULL, prenom VARCHAR(20) NOT NULL, tele VARCHAR(15) NOT NULL, date_naissance DATE NOT NULL, email VARCHAR(50) NOT NULL, roles JSON NOT NULL, mot_de_passe VARCHAR(120) NOT NULL, dernier_connection DATETIME DEFAULT NULL, desactive TINYINT(1) DEFAULT NULL, descr VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_FCEC9EFE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pneu (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(70) NOT NULL, type_vehicule VARCHAR(50) NOT NULL, image VARCHAR(255) NOT NULL, slug VARCHAR(128) NOT NULL, saison VARCHAR(20) NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL, quantite_stock INT NOT NULL, description LONGTEXT NOT NULL, date_ajout DATE NOT NULL, UNIQUE INDEX UNIQ_30D783FB989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id BIGINT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE technicien (id BIGINT NOT NULL, date_recrutement DATE NOT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adherent ADD CONSTRAINT FK_90D3F060BF396750 FOREIGN KEY (id) REFERENCES personne (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `admin` ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES personne (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418BF396750 FOREIGN KEY (id) REFERENCES pneu (id)');
        $this->addSql('ALTER TABLE pneu ADD CONSTRAINT FK_30D783FBBF396750 FOREIGN KEY (id) REFERENCES caracteristique (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES personne (id)');
        $this->addSql('ALTER TABLE technicien ADD CONSTRAINT FK_96282C4CBF396750 FOREIGN KEY (id) REFERENCES personne (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adherent DROP FOREIGN KEY FK_90D3F060BF396750');
        $this->addSql('ALTER TABLE `admin` DROP FOREIGN KEY FK_880E0D76BF396750');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418BF396750');
        $this->addSql('ALTER TABLE pneu DROP FOREIGN KEY FK_30D783FBBF396750');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE technicien DROP FOREIGN KEY FK_96282C4CBF396750');
        $this->addSql('DROP TABLE adherent');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE caracteristique');
        $this->addSql('DROP TABLE personne');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE pneu');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE technicien');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
