<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240407001955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, pneu_id INT NOT NULL, adherent_id BIGINT DEFAULT NULL, note INT NOT NULL, commentaire LONGTEXT DEFAULT NULL, date_creation DATETIME NOT NULL, author VARCHAR(20) DEFAULT NULL, email VARCHAR(40) DEFAULT NULL, INDEX IDX_8F91ABF0D7C9D5CE (pneu_id), INDEX IDX_8F91ABF025F06C53 (adherent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0D7C9D5CE FOREIGN KEY (pneu_id) REFERENCES pneu (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF025F06C53 FOREIGN KEY (adherent_id) REFERENCES adherent (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0D7C9D5CE');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF025F06C53');
        $this->addSql('DROP TABLE avis');
    }
}
