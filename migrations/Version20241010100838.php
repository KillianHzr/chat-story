<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241010100838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE choice (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, story_id INTEGER DEFAULT NULL, choice_text CLOB NOT NULL, vote_count INTEGER NOT NULL, is_selected BOOLEAN NOT NULL, CONSTRAINT FK_C1AB5A92AA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C1AB5A92AA5D4036 ON choice (story_id)');
        $this->addSql('CREATE TABLE message (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, room_id INTEGER DEFAULT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B6BD307F54177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B6BD307FA76ED395 ON message (user_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F54177093 ON message (room_id)');
        $this->addSql('CREATE TABLE room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, timer INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_number INTEGER NOT NULL, is_active BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE story (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, room_id INTEGER NOT NULL, story_text CLOB NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, CONSTRAINT FK_EB56043854177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_EB56043854177093 ON story (room_id)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('CREATE TABLE vote (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, choice_id INTEGER DEFAULT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_5A108564A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5A108564998666D1 FOREIGN KEY (choice_id) REFERENCES choice (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5A108564A76ED395 ON vote (user_id)');
        $this->addSql('CREATE INDEX IDX_5A108564998666D1 ON vote (choice_id)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE choice');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE story');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE vote');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
