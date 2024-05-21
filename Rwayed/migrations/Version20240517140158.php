<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517140158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE code_promo (id INT AUTO_INCREMENT NOT NULL, commande_id INT DEFAULT NULL, code VARCHAR(10) NOT NULL, description VARCHAR(200) DEFAULT NULL, pourcentge INT NOT NULL, date_expiration DATETIME NOT NULL, status VARCHAR(10) NOT NULL, INDEX IDX_5C4683B782EA2E54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE code_promo ADD CONSTRAINT FK_5C4683B782EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE code_promo DROP FOREIGN KEY FK_5C4683B782EA2E54');
        $this->addSql('DROP TABLE code_promo');
    }
}
