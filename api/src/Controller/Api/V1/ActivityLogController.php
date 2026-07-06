<?php

namespace App\Controller\Api\V1;

use App\Entity\ActivityLog;
use App\Repository\ActivityLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/activity-logs')]
class ActivityLogController extends AbstractController
{
    #[Route('', name: 'api_v1_activity_logs_list', methods: ['GET'])]
    public function index(Request $request, ActivityLogRepository $repo): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $perPage = min(100, max(1, $request->query->getInt('perPage', 10)));

        $qb = $repo->createQueryBuilder('l');
        $total = (clone $qb)->select('COUNT(l.id)')->getQuery()->getSingleScalarResult();
        $logs = $qb->select('l')
            ->orderBy('l.actionTime', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        $data = array_map(fn(ActivityLog $log) => [
            'id' => $log->getId(),
            'actionTime' => $log->getActionTime()?->format('c'),
            'actionFlag' => $log->getActionFlag(),
            'objectRepr' => $log->getObjectRepr(),
            'changeMessage' => $log->getChangeMessage(),
            'user' => $log->getUser() ? ['id' => $log->getUser()->getId(), 'name' => $log->getUser()->getName()] : null,
            'contentType' => $log->getContentType() ? ['appLabel' => $log->getContentType()->getAppLabel()] : null,
        ], $logs);

        return $this->json([
            'logs' => $data,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'total' => (int) $total,
                'lastPage' => (int) ceil($total / $perPage),
            ],
        ]);
    }
}
