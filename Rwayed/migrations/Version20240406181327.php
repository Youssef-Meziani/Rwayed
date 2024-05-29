<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240406181327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pneu_fav_list (adherent_id BIGINT NOT NULL, pneu_id INT NOT NULL, date_ajout DATE NOT NULL, INDEX IDX_D11A00BE25F06C53 (adherent_id), INDEX IDX_D11A00BED7C9D5CE (pneu_id), PRIMARY KEY(adherent_id, pneu_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pneu_fav_list ADD CONSTRAINT FK_D11A00BE25F06C53 FOREIGN KEY (adherent_id) REFERENCES adherent (id)');
        $this->addSql('ALTER TABLE pneu_fav_list ADD CONSTRAINT FK_D11A00BED7C9D5CE FOREIGN KEY (pneu_id) REFERENCES pneu (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pneu_fav_list DROP FOREIGN KEY FK_D11A00BE25F06C53');
        $this->addSql('ALTER TABLE pneu_fav_list DROP FOREIGN KEY FK_D11A00BED7C9D5CE');
        $this->addSql('DROP TABLE pneu_fav_list');
    }
}
