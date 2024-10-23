<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241017155725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE events_user (events_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E1C3D2339D6A1065 (events_id), INDEX IDX_E1C3D233A76ED395 (user_id), PRIMARY KEY(events_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE events_user ADD CONSTRAINT FK_E1C3D2339D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events_user ADD CONSTRAINT FK_E1C3D233A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events RENAME INDEX idx_5387574a9d86650f TO IDX_5387574AA76ED395');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events_user DROP FOREIGN KEY FK_E1C3D2339D6A1065');
        $this->addSql('ALTER TABLE events_user DROP FOREIGN KEY FK_E1C3D233A76ED395');
        $this->addSql('DROP TABLE events_user');
        $this->addSql('ALTER TABLE events RENAME INDEX idx_5387574aa76ed395 TO IDX_5387574A9D86650F');
    }
}
