<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211216175007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media_objects ADD answer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media_objects ADD CONSTRAINT FK_D3CD4ABAAA334807 FOREIGN KEY (answer_id) REFERENCES answers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D3CD4ABAAA334807 ON media_objects (answer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE media_objects DROP CONSTRAINT FK_D3CD4ABAAA334807');
        $this->addSql('DROP INDEX IDX_D3CD4ABAAA334807');
        $this->addSql('ALTER TABLE media_objects DROP answer_id');
    }
}
