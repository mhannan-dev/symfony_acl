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

        $activeUsers = (int) $conn->fetchOne('SELECT COUNT(*) FROM users WHERE is_active = 1');
        $inactiveUsers = (int) $conn->fetchOne('SELECT COUNT(*) FROM users WHERE is_active = 0');

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
             GROUP BY DATE(action_time)
             ORDER BY date ASC'
        );

        return $this->json([
            'stats' => [
                'users' => $userRepo->count([]),
                'groups' => $groupRepo->count([]),
                'permissions' => $permissionRepo->count([]),
                'recentActivity' => $logRepo->count([]),
            ],
            'charts' => [
                'usersPerGroup' => $usersPerGroup,
                'userStatus' => [
                    ['label' => 'Active', 'count' => $activeUsers],
                    ['label' => 'Inactive', 'count' => $inactiveUsers],
                ],
                'permissionsPerContentType' => $permsPerContentType,
                'activityLast7Days' => $recentLogs,
            ],
        ]);
    }
}
