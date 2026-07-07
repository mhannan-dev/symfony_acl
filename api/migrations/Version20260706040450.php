<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260706040450 extends AbstractMigration
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

        $toSchema->getTable('groups')->dropIndex('UNIQ_F06D39705E237E06');

        $diff = (new \Doctrine\DBAL\Schema\Comparator($this->connection->getDatabasePlatform()))->compareSchemas($fromSchema, $toSchema);
        $queries = $this->connection->getDatabasePlatform()->getAlterSchemaSQL($diff);
        foreach ($queries as $query) {
            $this->addSql($query);
        }
    }

    public function down(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();
        $fromSchema = $sm->introspectSchema();
        $toSchema = clone $fromSchema;

        $toSchema->getTable('groups')->addUniqueIndex(['name'], 'UNIQ_F06D39705E237E06');

        $diff = (new \Doctrine\DBAL\Schema\Comparator($this->connection->getDatabasePlatform()))->compareSchemas($fromSchema, $toSchema);
        $queries = $this->connection->getDatabasePlatform()->getAlterSchemaSQL($diff);
        foreach ($queries as $query) {
            $this->addSql($query);
        }
    }
}
