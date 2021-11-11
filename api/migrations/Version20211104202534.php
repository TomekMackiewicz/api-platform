<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211104202534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE test_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE exam_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE exam (id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, summary TEXT DEFAULT NULL, duration INT DEFAULT NULL, next_submission_after INT DEFAULT NULL, ttl INT DEFAULT NULL, use_pagination BOOLEAN DEFAULT NULL, questions_per_page INT DEFAULT NULL, shuffle_questions BOOLEAN DEFAULT NULL, immediate_answers BOOLEAN DEFAULT NULL, restrict_submissions BOOLEAN DEFAULT NULL, allowed_submissions INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE test');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE exam_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE test_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE test (id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, summary TEXT DEFAULT NULL, duration INT DEFAULT NULL, next_submission_after INT DEFAULT NULL, ttl INT DEFAULT NULL, use_pagination BOOLEAN DEFAULT NULL, questions_per_page INT DEFAULT NULL, shuffle_questions BOOLEAN DEFAULT NULL, immediate_answers BOOLEAN DEFAULT NULL, restrict_submissions BOOLEAN DEFAULT NULL, allowed_submissions INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE exam');
    }
}
