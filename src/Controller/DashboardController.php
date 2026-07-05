<?php

namespace App\Controller;

use App\Repository\ActivityLogRepository;
use App\Repository\GroupRepository;
use App\Repository\PermissionRepository;
use App\Repository\UserRepository;
use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        InertiaInterface $inertia,
        UserRepository $userRepo,
        GroupRepository $groupRepo,
        PermissionRepository $permissionRepo,
        ActivityLogRepository $logRepo,
    ) {
        return $inertia->render('Dashboard', [
            'stats' => [
                'users' => $userRepo->count([]),
                'groups' => $groupRepo->count([]),
                'permissions' => $permissionRepo->count([]),
                'recentActivity' => $logRepo->count([]),
            ],
        ]);
    }
}
