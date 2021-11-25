<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211125180040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exam_topic DROP CONSTRAINT fk_cfa5d8ab1f55203d');
        $this->addSql('DROP SEQUENCE topics_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE categories (id INT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE exam_category (exam_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(exam_id, category_id))');
        $this->addSql('CREATE INDEX IDX_452856F2578D5E91 ON exam_category (exam_id)');
        $this->addSql('CREATE INDEX IDX_452856F212469DE2 ON exam_category (category_id)');
        $this->addSql('ALTER TABLE exam_category ADD CONSTRAINT FK_452856F2578D5E91 FOREIGN KEY (exam_id) REFERENCES exams (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exam_category ADD CONSTRAINT FK_452856F212469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE exam_topic');
        $this->addSql('DROP TABLE topics');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE exam_category DROP CONSTRAINT FK_452856F212469DE2');
        $this->addSql('DROP SEQUENCE categories_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE topics_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE exam_topic (exam_id INT NOT NULL, topic_id INT NOT NULL, PRIMARY KEY(exam_id, topic_id))');
        $this->addSql('CREATE INDEX idx_cfa5d8ab578d5e91 ON exam_topic (exam_id)');
        $this->addSql('CREATE INDEX idx_cfa5d8ab1f55203d ON exam_topic (topic_id)');
        $this->addSql('CREATE TABLE topics (id INT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE exam_topic ADD CONSTRAINT fk_cfa5d8ab578d5e91 FOREIGN KEY (exam_id) REFERENCES exams (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exam_topic ADD CONSTRAINT fk_cfa5d8ab1f55203d FOREIGN KEY (topic_id) REFERENCES topics (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE exam_category');
    }
}
