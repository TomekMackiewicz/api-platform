<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211201192312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_exam (category_id INT NOT NULL, exam_id INT NOT NULL, PRIMARY KEY(category_id, exam_id))');
        $this->addSql('CREATE INDEX IDX_B320DC8112469DE2 ON category_exam (category_id)');
        $this->addSql('CREATE INDEX IDX_B320DC81578D5E91 ON category_exam (exam_id)');
        $this->addSql('ALTER TABLE category_exam ADD CONSTRAINT FK_B320DC8112469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_exam ADD CONSTRAINT FK_B320DC81578D5E91 FOREIGN KEY (exam_id) REFERENCES exams (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE exam_category');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE exam_category (exam_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(exam_id, category_id))');
        $this->addSql('CREATE INDEX idx_452856f2578d5e91 ON exam_category (exam_id)');
        $this->addSql('CREATE INDEX idx_452856f212469de2 ON exam_category (category_id)');
        $this->addSql('ALTER TABLE exam_category ADD CONSTRAINT fk_452856f2578d5e91 FOREIGN KEY (exam_id) REFERENCES exams (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exam_category ADD CONSTRAINT fk_452856f212469de2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE category_exam');
    }
}
