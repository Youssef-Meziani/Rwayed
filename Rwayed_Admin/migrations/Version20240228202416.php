<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240228202416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE caracteristique MODIFY id_cara INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON caracteristique');
        $this->addSql('ALTER TABLE caracteristique CHANGE id_cara id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE caracteristique ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418BBF79FF9');
        $this->addSql('DROP INDEX IDX_14B78418BBF79FF9 ON photo');
        $this->addSql('ALTER TABLE photo DROP id_pneu');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418BF396750 FOREIGN KEY (id) REFERENCES pneu (id)');
        $this->addSql('ALTER TABLE pneu MODIFY id_pneu INT NOT NULL');
        $this->addSql('ALTER TABLE pneu DROP FOREIGN KEY FK_30D783FBED5E705E');
        $this->addSql('DROP INDEX IDX_30D783FBED5E705E ON pneu');
        $this->addSql('DROP INDEX `primary` ON pneu');
        $this->addSql('ALTER TABLE pneu DROP id_cara, CHANGE id_pneu id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE pneu ADD CONSTRAINT FK_30D783FBBF396750 FOREIGN KEY (id) REFERENCES caracteristique (id)');
        $this->addSql('ALTER TABLE pneu ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418BF396750');
        $this->addSql('ALTER TABLE photo ADD id_pneu INT NOT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418BBF79FF9 FOREIGN KEY (id_pneu) REFERENCES pneu (id_pneu) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_14B78418BBF79FF9 ON photo (id_pneu)');
        $this->addSql('ALTER TABLE caracteristique MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON caracteristique');
        $this->addSql('ALTER TABLE caracteristique CHANGE id id_cara INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE caracteristique ADD PRIMARY KEY (id_cara)');
        $this->addSql('ALTER TABLE pneu MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE pneu DROP FOREIGN KEY FK_30D783FBBF396750');
        $this->addSql('DROP INDEX `PRIMARY` ON pneu');
        $this->addSql('ALTER TABLE pneu ADD id_cara INT NOT NULL, CHANGE id id_pneu INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE pneu ADD CONSTRAINT FK_30D783FBED5E705E FOREIGN KEY (id_cara) REFERENCES caracteristique (id_cara) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_30D783FBED5E705E ON pneu (id_cara)');
        $this->addSql('ALTER TABLE pneu ADD PRIMARY KEY (id_pneu)');
    }
}
