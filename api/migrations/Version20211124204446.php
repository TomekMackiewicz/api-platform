<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211124204446 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE topics_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE exam_topic (exam_id INT NOT NULL, topic_id INT NOT NULL, PRIMARY KEY(exam_id, topic_id))');
        $this->addSql('CREATE INDEX IDX_CFA5D8AB578D5E91 ON exam_topic (exam_id)');
        $this->addSql('CREATE INDEX IDX_CFA5D8AB1F55203D ON exam_topic (topic_id)');
        $this->addSql('CREATE TABLE topics (id INT NOT NULL, label TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE exam_topic ADD CONSTRAINT FK_CFA5D8AB578D5E91 FOREIGN KEY (exam_id) REFERENCES exams (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exam_topic ADD CONSTRAINT FK_CFA5D8AB1F55203D FOREIGN KEY (topic_id) REFERENCES topics (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE exam_topic DROP CONSTRAINT FK_CFA5D8AB1F55203D');
        $this->addSql('DROP SEQUENCE topics_id_seq CASCADE');
        $this->addSql('DROP TABLE exam_topic');
        $this->addSql('DROP TABLE topics');
    }
}
