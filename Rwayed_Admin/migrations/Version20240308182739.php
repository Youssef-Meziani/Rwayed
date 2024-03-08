<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240308182739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE informations (id INT AUTO_INCREMENT NOT NULL, id_adm_id BIGINT NOT NULL, titre VARCHAR(100) NOT NULL, date_envoi DATETIME NOT NULL, contenu LONGTEXT NOT NULL, INDEX IDX_6F96648968A32AC0 (id_adm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE informations_emails (informations_id INT NOT NULL, emails_id INT NOT NULL, INDEX IDX_65AD85990587D82 (informations_id), INDEX IDX_65AD859561F5F0 (emails_id), PRIMARY KEY(informations_id, emails_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE informations ADD CONSTRAINT FK_6F96648968A32AC0 FOREIGN KEY (id_adm_id) REFERENCES `admin` (id)');
        $this->addSql('ALTER TABLE informations_emails ADD CONSTRAINT FK_65AD85990587D82 FOREIGN KEY (informations_id) REFERENCES informations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE informations_emails ADD CONSTRAINT FK_65AD859561F5F0 FOREIGN KEY (emails_id) REFERENCES emails (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE informations DROP FOREIGN KEY FK_6F96648968A32AC0');
        $this->addSql('ALTER TABLE informations_emails DROP FOREIGN KEY FK_65AD85990587D82');
        $this->addSql('ALTER TABLE informations_emails DROP FOREIGN KEY FK_65AD859561F5F0');
        $this->addSql('DROP TABLE informations');
        $this->addSql('DROP TABLE informations_emails');
    }
}
