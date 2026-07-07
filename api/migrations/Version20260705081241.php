<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260705081241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity_logs (id INT AUTO_INCREMENT NOT NULL, action_time DATETIME NOT NULL, object_id LONGTEXT DEFAULT NULL, object_repr VARCHAR(255) NOT NULL, action_flag SMALLINT NOT NULL, change_message LONGTEXT NOT NULL, user_id INT NOT NULL, content_type_id INT DEFAULT NULL, INDEX IDX_F34B1DCEA76ED395 (user_id), INDEX IDX_F34B1DCE1A445520 (content_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE content_types (id INT AUTO_INCREMENT NOT NULL, app_label VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE group_permissions (id INT AUTO_INCREMENT NOT NULL, group_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_855D3AEFE54D947 (group_id), INDEX IDX_855D3AEFED90CCA (permission_id), UNIQUE INDEX unique_group_permission (group_id, permission_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `groups` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F06D39705E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE permissions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, codename VARCHAR(255) NOT NULL, content_type_id INT NOT NULL, INDEX IDX_2DEDCC6F1A445520 (content_type_id), UNIQUE INDEX unique_content_codename (content_type_id, codename), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_groups (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_953F224DA76ED395 (user_id), INDEX IDX_953F224DFE54D947 (group_id), UNIQUE INDEX unique_user_group (user_id, group_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_permissions (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_84F605FAA76ED395 (user_id), INDEX IDX_84F605FAFED90CCA (permission_id), UNIQUE INDEX unique_user_permission (user_id, permission_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE activity_logs ADD CONSTRAINT FK_F34B1DCEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE activity_logs ADD CONSTRAINT FK_F34B1DCE1A445520 FOREIGN KEY (content_type_id) REFERENCES content_types (id)');
        $this->addSql('ALTER TABLE group_permissions ADD CONSTRAINT FK_855D3AEFE54D947 FOREIGN KEY (group_id) REFERENCES `groups` (id)');
        $this->addSql('ALTER TABLE group_permissions ADD CONSTRAINT FK_855D3AEFED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id)');
        $this->addSql('ALTER TABLE permissions ADD CONSTRAINT FK_2DEDCC6F1A445520 FOREIGN KEY (content_type_id) REFERENCES content_types (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_groups ADD CONSTRAINT FK_953F224DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_groups ADD CONSTRAINT FK_953F224DFE54D947 FOREIGN KEY (group_id) REFERENCES `groups` (id)');
        $this->addSql('ALTER TABLE user_permissions ADD CONSTRAINT FK_84F605FAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_permissions ADD CONSTRAINT FK_84F605FAFED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_logs DROP FOREIGN KEY FK_F34B1DCEA76ED395');
        $this->addSql('ALTER TABLE activity_logs DROP FOREIGN KEY FK_F34B1DCE1A445520');
        $this->addSql('ALTER TABLE group_permissions DROP FOREIGN KEY FK_855D3AEFE54D947');
        $this->addSql('ALTER TABLE group_permissions DROP FOREIGN KEY FK_855D3AEFED90CCA');
        $this->addSql('ALTER TABLE permissions DROP FOREIGN KEY FK_2DEDCC6F1A445520');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE user_groups DROP FOREIGN KEY FK_953F224DA76ED395');
        $this->addSql('ALTER TABLE user_groups DROP FOREIGN KEY FK_953F224DFE54D947');
        $this->addSql('ALTER TABLE user_permissions DROP FOREIGN KEY FK_84F605FAA76ED395');
        $this->addSql('ALTER TABLE user_permissions DROP FOREIGN KEY FK_84F605FAFED90CCA');
        $this->addSql('DROP TABLE activity_logs');
        $this->addSql('DROP TABLE content_types');
        $this->addSql('DROP TABLE group_permissions');
        $this->addSql('DROP TABLE `groups`');
        $this->addSql('DROP TABLE permissions');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE user_groups');
        $this->addSql('DROP TABLE user_permissions');
        $this->addSql('DROP TABLE users');
    }
}
