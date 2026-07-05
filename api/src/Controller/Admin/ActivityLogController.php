<?php

namespace App\Controller\Admin;

use App\Repository\ActivityLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/activity-logs')]
class ActivityLogController extends AbstractController
{
    #[Route('', name: 'app_admin_activity_logs')]
    public function index( ActivityLogRepository $repo)
    {
        return $inertia->render('ActivityLogs/Index', [
            'logs' => $repo->findBy([], ['actionTime' => 'DESC'], 100),
        ]);
    }
}
