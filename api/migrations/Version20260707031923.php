<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260707031923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();
        $fromSchema = $sm->introspectSchema();
        $toSchema = clone $fromSchema;

        $toSchema->getTable('groups')->addColumn('deleted_at', 'datetime', ['notnull' => false]);
        $toSchema->getTable('permissions')->addColumn('group_name', 'string', ['length' => 255, 'notnull' => false]);
        $toSchema->getTable('permissions')->addColumn('deleted_at', 'datetime', ['notnull' => false]);

        $platform = $this->connection->getDatabasePlatform();
        $diff = (new \Doctrine\DBAL\Schema\Comparator($platform))->compareSchemas($fromSchema, $toSchema);
        $queries = $platform->getAlterSchemaSQL($diff);
        foreach ($queries as $query) {
            $this->addSql($query);
        }
    }

    public function down(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();
        $fromSchema = $sm->introspectSchema();
        $toSchema = clone $fromSchema;

        $toSchema->getTable('groups')->dropColumn('deleted_at');
        $toSchema->getTable('permissions')->dropColumn('group_name');
        $toSchema->getTable('permissions')->dropColumn('deleted_at');

        $platform = $this->connection->getDatabasePlatform();
        $diff = (new \Doctrine\DBAL\Schema\Comparator($platform))->compareSchemas($fromSchema, $toSchema);
        $queries = $platform->getAlterSchemaSQL($diff);
        foreach ($queries as $query) {
            $this->addSql($query);
        }
    }
}
