<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240313214918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personne ADD is_verified TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418BBF79FF9');
        $this->addSql('ALTER TABLE photo CHANGE path path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418BBF79FF9 FOREIGN KEY (id_pneu) REFERENCES pneu (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personne DROP is_verified');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418BBF79FF9');
        $this->addSql('ALTER TABLE photo CHANGE path path VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418BBF79FF9 FOREIGN KEY (id_pneu) REFERENCES pneu (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
