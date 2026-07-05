<?php

namespace App\Controller\Api;

use App\Entity\ActivityLog;
use App\Repository\ActivityLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/activity-logs')]
class ActivityLogController extends AbstractController
{
    #[Route('', name: 'api_activity_logs_list', methods: ['GET'])]
    public function index(ActivityLogRepository $repo): JsonResponse
    {
        $logs = $repo->findBy([], ['actionTime' => 'DESC'], 100);
        $data = array_map(fn(ActivityLog $log) => [
            'id' => $log->getId(),
            'actionTime' => $log->getActionTime()?->format('c'),
            'actionFlag' => $log->getActionFlag(),
            'objectRepr' => $log->getObjectRepr(),
            'changeMessage' => $log->getChangeMessage(),
            'user' => $log->getUser() ? ['id' => $log->getUser()->getId(), 'name' => $log->getUser()->getName()] : null,
            'contentType' => $log->getContentType() ? ['appLabel' => $log->getContentType()->getAppLabel()] : null,
        ], $logs);

        return $this->json(['logs' => $data]);
    }
}
