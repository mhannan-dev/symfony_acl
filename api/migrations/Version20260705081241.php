<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260705081241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $newSchema = new Schema();

        $table = $newSchema->createTable('users');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('email', 'string', ['length' => 255]);
        $table->addColumn('password', 'string', ['length' => 255]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['email'], 'UNIQ_1483A5E9E7927C74');

        $table = $newSchema->createTable('groups');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['name'], 'UNIQ_F06D39705E237E06');

        $table = $newSchema->createTable('content_types');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('app_label', 'string', ['length' => 255]);
        $table->addColumn('model', 'string', ['length' => 255]);
        $table->setPrimaryKey(['id']);

        $table = $newSchema->createTable('permissions');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('codename', 'string', ['length' => 255]);
        $table->addColumn('content_type_id', 'integer');
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('content_types', ['content_type_id'], ['id'], [], 'FK_2DEDCC6F1A445520');
        $table->addUniqueIndex(['content_type_id', 'codename'], 'unique_content_codename');

        $table = $newSchema->createTable('group_permissions');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('group_id', 'integer');
        $table->addColumn('permission_id', 'integer');
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('groups', ['group_id'], ['id'], [], 'FK_855D3AEFE54D947');
        $table->addForeignKeyConstraint('permissions', ['permission_id'], ['id'], [], 'FK_855D3AEFED90CCA');
        $table->addUniqueIndex(['group_id', 'permission_id'], 'unique_group_permission');

        $table = $newSchema->createTable('user_groups');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('user_id', 'integer');
        $table->addColumn('group_id', 'integer');
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('users', ['user_id'], ['id'], [], 'FK_953F224DA76ED395');
        $table->addForeignKeyConstraint('groups', ['group_id'], ['id'], [], 'FK_953F224DFE54D947');
        $table->addUniqueIndex(['user_id', 'group_id'], 'unique_user_group');

        $table = $newSchema->createTable('user_permissions');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('user_id', 'integer');
        $table->addColumn('permission_id', 'integer');
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('users', ['user_id'], ['id'], [], 'FK_84F605FAA76ED395');
        $table->addForeignKeyConstraint('permissions', ['permission_id'], ['id'], [], 'FK_84F605FAFED90CCA');
        $table->addUniqueIndex(['user_id', 'permission_id'], 'unique_user_permission');

        $table = $newSchema->createTable('activity_logs');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('action_time', 'datetime');
        $table->addColumn('object_id', 'text', ['notnull' => false]);
        $table->addColumn('object_repr', 'string', ['length' => 255]);
        $table->addColumn('action_flag', 'smallint');
        $table->addColumn('change_message', 'text');
        $table->addColumn('user_id', 'integer');
        $table->addColumn('content_type_id', 'integer', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('users', ['user_id'], ['id'], [], 'FK_F34B1DCEA76ED395');
        $table->addForeignKeyConstraint('content_types', ['content_type_id'], ['id'], [], 'FK_F34B1DCE1A445520');

        $table = $newSchema->createTable('reset_password_request');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('selector', 'string', ['length' => 20]);
        $table->addColumn('hashed_token', 'string', ['length' => 100]);
        $table->addColumn('requested_at', 'datetime');
        $table->addColumn('expires_at', 'datetime');
        $table->addColumn('user_id', 'integer');
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('users', ['user_id'], ['id'], [], 'FK_7CE748AA76ED395');

        $platform = $this->connection->getDatabasePlatform();
        $isMysql = $platform instanceof \Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
        $queries = $newSchema->toSql($platform);

        foreach ($queries as $query) {
            if ($isMysql && str_starts_with($query, 'CREATE TABLE')) {
                $this->addSql($query.' DEFAULT CHARACTER SET utf8mb4');
            } else {
                $this->addSql($query);
            }
        }
    }

    public function down(Schema $schema): void
    {
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
