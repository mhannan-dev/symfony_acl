<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260707034411 extends AbstractMigration
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

        $toSchema->getTable('users')->addColumn('is_active', 'boolean', ['default' => 1]);

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

        $toSchema->getTable('users')->dropColumn('is_active');

        $platform = $this->connection->getDatabasePlatform();
        $diff = (new \Doctrine\DBAL\Schema\Comparator($platform))->compareSchemas($fromSchema, $toSchema);
        $queries = $platform->getAlterSchemaSQL($diff);
        foreach ($queries as $query) {
            $this->addSql($query);
        }
    }
}
