<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260707081943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity_logs (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, action_time DATETIME NOT NULL, object_id CLOB DEFAULT NULL, object_repr VARCHAR(255) NOT NULL, action_flag SMALLINT NOT NULL, change_message CLOB NOT NULL, user_id INTEGER NOT NULL, content_type_id INTEGER DEFAULT NULL, CONSTRAINT FK_F34B1DCEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F34B1DCE1A445520 FOREIGN KEY (content_type_id) REFERENCES content_types (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F34B1DCEA76ED395 ON activity_logs (user_id)');
        $this->addSql('CREATE INDEX IDX_F34B1DCE1A445520 ON activity_logs (content_type_id)');
        $this->addSql('CREATE TABLE content_types (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, app_label VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE group_permissions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, group_id INTEGER NOT NULL, permission_id INTEGER NOT NULL, CONSTRAINT FK_855D3AEFE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_855D3AEFED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_855D3AEFE54D947 ON group_permissions (group_id)');
        $this->addSql('CREATE INDEX IDX_855D3AEFED90CCA ON group_permissions (permission_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_group_permission ON group_permissions (group_id, permission_id)');
        $this->addSql('CREATE TABLE groups (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status BOOLEAN DEFAULT 1 NOT NULL, deleted_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE TABLE permissions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, codename VARCHAR(255) NOT NULL, group_name VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, content_type_id INTEGER NOT NULL, CONSTRAINT FK_2DEDCC6F1A445520 FOREIGN KEY (content_type_id) REFERENCES content_types (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2DEDCC6F1A445520 ON permissions (content_type_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_content_codename ON permissions (content_type_id, codename)');
        $this->addSql('CREATE TABLE reset_password_request (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('CREATE TABLE user_groups (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, group_id INTEGER NOT NULL, CONSTRAINT FK_953F224DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_953F224DFE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_953F224DA76ED395 ON user_groups (user_id)');
        $this->addSql('CREATE INDEX IDX_953F224DFE54D947 ON user_groups (group_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_user_group ON user_groups (user_id, group_id)');
        $this->addSql('CREATE TABLE user_permissions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, permission_id INTEGER NOT NULL, CONSTRAINT FK_84F605FAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_84F605FAFED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_84F605FAA76ED395 ON user_permissions (user_id)');
        $this->addSql('CREATE INDEX IDX_84F605FAFED90CCA ON user_permissions (permission_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_user_permission ON user_permissions (user_id, permission_id)');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, is_active BOOLEAN DEFAULT 1 NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE activity_logs');
        $this->addSql('DROP TABLE content_types');
        $this->addSql('DROP TABLE group_permissions');
        $this->addSql('DROP TABLE groups');
        $this->addSql('DROP TABLE permissions');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE user_groups');
        $this->addSql('DROP TABLE user_permissions');
        $this->addSql('DROP TABLE users');
    }
}
