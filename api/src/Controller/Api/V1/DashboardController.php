<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Repository\ActivityLogRepository;
use App\Repository\ContentTypeRepository;
use App\Repository\GroupRepository;
use App\Repository\PermissionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1')]
class DashboardController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('/dashboard/stats', name: 'api_v1_dashboard_stats', methods: ['GET'])]
    public function stats(
        UserRepository $userRepo,
        GroupRepository $groupRepo,
        PermissionRepository $permissionRepo,
        ContentTypeRepository $ctRepo,
        ActivityLogRepository $logRepo,
    ): JsonResponse {
        $conn = $this->em->getConnection();

        $usersPerGroup = $conn->fetchAllAssociative(
            'SELECT g.name, COUNT(ug.user_id) as count
             FROM `groups` g
             LEFT JOIN user_groups ug ON ug.group_id = g.id
             GROUP BY g.id, g.name
             ORDER BY count DESC'
        );


        $permsPerContentType = $conn->fetchAllAssociative(
            'SELECT ct.app_label, ct.model, COUNT(p.id) as count
             FROM content_types ct
             LEFT JOIN permissions p ON p.content_type_id = ct.id
             GROUP BY ct.id, ct.app_label, ct.model
             ORDER BY count DESC'
        );

        $recentLogs = $conn->fetchAllAssociative(
            'SELECT DATE_FORMAT(action_time, \'%Y-%m-%d\') as date, COUNT(*) as count
             FROM activity_logs
             WHERE action_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY date
             ORDER BY date ASC'
        );

        $userTreeRows = $conn->fetchAllAssociative(
            'SELECT u.id as user_id, u.name as user_name, g.id as group_id, g.name as group_name
             FROM users u
             LEFT JOIN user_groups ug ON ug.user_id = u.id
             LEFT JOIN `groups` g ON ug.group_id = g.id'
        );

        $groupsMap = [];
        $unassigned = [];

        foreach ($userTreeRows as $row) {
            $uId = 'user_' . $row['user_id'] . '_' . (int)$row['group_id'];
            $uName = $row['user_name'];
            $gId = $row['group_id'] ? 'group_' . $row['group_id'] : null;
            $gName = $row['group_name'];

            $userNode = ['id' => $uId, 'name' => $uName];

            if ($gId) {
                if (!isset($groupsMap[$gId])) {
                    $groupsMap[$gId] = [
                        'id' => $gId,
                        'name' => $gName,
                        'children' => []
                    ];
                }
                $groupsMap[$gId]['children'][] = $userNode;
            } else {
                $unassigned[] = $userNode;
            }
        }

        if (!empty($unassigned)) {
            $groupsMap['group_unassigned'] = [
                'id' => 'group_unassigned',
                'name' => 'Unassigned',
                'children' => $unassigned
            ];
        }

        $userTree = [
            'id' => 'root',
            'name' => 'Symfony ACL Users',
            'children' => array_values($groupsMap)
        ];

        // Schema Tree
        $schemaManager = $conn->createSchemaManager();
        $tables = $schemaManager->listTables();

        $schemaTreeNodes = [];
        foreach ($tables as $table) {
            $tableName = $table->getName();
            if ($tableName === 'doctrine_migration_versions') continue;
            
            $columnNodes = [];
            foreach ($table->getColumns() as $column) {
                $typeClass = get_class($column->getType());
                $typeName = str_replace('Type', '', basename(str_replace('\\', '/', $typeClass)));
                
                $columnNodes[] = [
                    'id' => 'col_' . $tableName . '_' . $column->getName(),
                    'name' => $column->getName(),
                    'type' => $typeName
                ];
            }
            $schemaTreeNodes[] = [
                'id' => 'tbl_' . $tableName,
                'name' => $tableName,
                'children' => $columnNodes
            ];
        }

        $schemaTree = [
            'id' => 'schema_root',
            'name' => 'Symfony ACL Database',
            'children' => $schemaTreeNodes
        ];

        return $this->json([
            'stats' => [
                'users' => $userRepo->count([]),
                'groups' => $groupRepo->count([]),
                'permissions' => $permissionRepo->count([]),
                'recentActivity' => $logRepo->count([]),
            ],
            'charts' => [
                'usersPerGroup' => $usersPerGroup,
                'permissionsPerContentType' => $permsPerContentType,
                'activityLast7Days' => $recentLogs,
                'userTree' => $userTree,
                'schemaTree' => $schemaTree,
            ],
        ]);
    }
}
