<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Repository\ActivityLogRepository;
use App\Repository\GroupRepository;
use App\Repository\PermissionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard/stats', name: 'api_v1_dashboard_stats', methods: ['GET'])]
    public function stats(
        UserRepository $userRepo,
        GroupRepository $groupRepo,
        PermissionRepository $permissionRepo,
        ActivityLogRepository $logRepo,
    ): JsonResponse {
        return $this->json([
            'stats' => [
                'users' => $userRepo->count([]),
                'groups' => $groupRepo->count([]),
                'permissions' => $permissionRepo->count([]),
                'recentActivity' => $logRepo->count([]),
            ],
        ]);
    }
}
