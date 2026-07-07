<?php

namespace App\Command;

use App\Entity\ContentType;
use App\Entity\Permission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sync-permissions',
    description: 'Scans all Doctrine entities and generates ContentTypes and default Permissions (add, change, delete, view).'
)]
class SyncPermissionsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Execute without saving to the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $isDryRun = $input->getOption('dry-run');
        
        $title = 'Syncing ContentTypes and Permissions';
        if ($isDryRun) {
            $title .= ' (DRY RUN)';
        }
        $io->title($title);

        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        $contentTypeRepo = $this->em->getRepository(ContentType::class);
        $permissionRepo = $this->em->getRepository(Permission::class);

        $createdContentTypes = 0;
        $createdPermissions = 0;

        $actions = [
            'add' => 'Can add',
            'change' => 'Can change',
            'delete' => 'Can delete',
            'view' => 'Can view'
        ];

        foreach ($metadatas as $metadata) {
            $className = $metadata->getName();

            // Skip mapped superclasses, external entities, or system-managed entities like ActivityLog
            if (!str_starts_with($className, 'App\\Entity\\') || $metadata->isMappedSuperclass) {
                continue;
            }

            // Exclude specific entities that shouldn't have manually managed permissions
            $excludedEntities = [
                \App\Entity\ActivityLog::class,
            ];

            if (in_array($className, $excludedEntities)) {
                continue;
            }

            $reflection = $metadata->getReflectionClass();
            $modelName = $reflection->getShortName();

            // Convert CamelCase to snake_case for the model identifier
            $model = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $modelName));
            $appLabel = 'app';

            // Find or create ContentType
            $contentType = $contentTypeRepo->findOneBy(['appLabel' => $appLabel, 'model' => $model]);

            if (!$contentType) {
                $contentType = new ContentType();
                $contentType->setAppLabel($appLabel);
                $contentType->setModel($model);
                if (!$isDryRun) {
                    $this->em->persist($contentType);
                    // Flush immediately so we have the ID for the permission lookup/creation
                    $this->em->flush();
                } else {
                    $io->note(sprintf('Would create ContentType: %s.%s', $appLabel, $model));
                }
                $createdContentTypes++;
            }

            // Create default permissions
            foreach ($actions as $action => $actionLabel) {
                $codename = sprintf('%s_%s', $action, $model);

                $permission = $permissionRepo->findOneBy([
                    'contentType' => $contentType,
                    'codename' => $codename
                ]);

                if (!$permission) {
                    $permission = new Permission();
                    $permission->setContentType($contentType);
                    $permission->setCodename($codename);
                    $permission->setName(sprintf('%s %s', $actionLabel, strtolower($modelName)));
                    $permission->setGroupName(ucfirst($appLabel));
                    
                    if (!$isDryRun) {
                        $this->em->persist($permission);
                    } else {
                        $io->note(sprintf('Would create Permission: %s (Group: %s)', $codename, ucfirst($appLabel)));
                    }
                    $createdPermissions++;
                }
            }
        }

        if (!$isDryRun) {
            $this->em->flush();
        }

        if ($isDryRun) {
            $io->success(sprintf('Dry-run complete! Would have created %d ContentTypes and %d Permissions.', $createdContentTypes, $createdPermissions));
        } else {
            $io->success(sprintf('Done! Created %d ContentTypes and %d Permissions.', $createdContentTypes, $createdPermissions));
        }

        return Command::SUCCESS;
    }
}
