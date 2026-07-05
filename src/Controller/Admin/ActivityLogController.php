<?php

namespace App\Controller\Admin;

use App\Repository\ActivityLogRepository;
use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/activity-logs')]
class ActivityLogController extends AbstractController
{
    #[Route('', name: 'app_admin_activity_logs')]
    public function index(InertiaInterface $inertia, ActivityLogRepository $repo)
    {
        return $inertia->render('ActivityLogs/Index', [
            'logs' => $repo->findBy([], ['actionTime' => 'DESC'], 100),
        ]);
    }
}
