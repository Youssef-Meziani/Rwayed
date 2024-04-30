<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240408201233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse ADD city VARCHAR(30) NOT NULL, ADD street VARCHAR(30) NOT NULL, DROP ville, DROP rue, CHANGE code_postale postcode INT NOT NULL, CHANGE default_adresse setasmydefaultaddress TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse ADD ville VARCHAR(30) NOT NULL, ADD rue VARCHAR(30) NOT NULL, DROP city, DROP street, CHANGE postcode code_postale INT NOT NULL, CHANGE setasmydefaultaddress default_adresse TINYINT(1) NOT NULL');
    }
}
