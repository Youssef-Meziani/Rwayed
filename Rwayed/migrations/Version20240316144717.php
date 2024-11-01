<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240316144717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418BBF79FF9');
        $this->addSql('ALTER TABLE photo ADD updated_at DATETIME DEFAULT NULL, CHANGE path path VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418BBF79FF9 FOREIGN KEY (id_pneu) REFERENCES pneu (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418BBF79FF9');
        $this->addSql('ALTER TABLE photo DROP updated_at, CHANGE path path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418BBF79FF9 FOREIGN KEY (id_pneu) REFERENCES pneu (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
