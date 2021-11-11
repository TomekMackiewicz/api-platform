<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211109124226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE exam_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE exams_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE exams (id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, summary TEXT DEFAULT NULL, duration INT DEFAULT NULL, next_submission_after INT DEFAULT NULL, ttl INT DEFAULT NULL, use_pagination BOOLEAN DEFAULT NULL, questions_per_page INT DEFAULT NULL, shuffle_questions BOOLEAN DEFAULT NULL, immediate_answers BOOLEAN DEFAULT NULL, restrict_submissions BOOLEAN DEFAULT NULL, allowed_submissions INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE questions (id INT NOT NULL, exam_id INT DEFAULT NULL, label TEXT NOT NULL, description TEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, hint TEXT DEFAULT NULL, is_required BOOLEAN DEFAULT NULL, shuffle_answers BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8ADC54D5578D5E91 ON questions (exam_id)');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, status INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5578D5E91 FOREIGN KEY (exam_id) REFERENCES exams (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE questions DROP CONSTRAINT FK_8ADC54D5578D5E91');
        $this->addSql('DROP SEQUENCE exams_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE exam_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP TABLE exams');
        $this->addSql('DROP TABLE questions');
        $this->addSql('DROP TABLE users');
    }
}
