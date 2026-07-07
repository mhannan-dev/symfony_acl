<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Entity\ActivityLog;
use App\Repository\ActivityLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/activity-logs')]
class ActivityLogController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('', name: 'api_v1_activity_logs_list', methods: ['GET'])]
    #[IsGranted('view_activity_log')]
    public function index(Request $request, ActivityLogRepository $repo): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $perPage = min(100, max(1, $request->query->getInt('perPage', 10)));
        $search = $request->query->getString('search', '');

        $qb = $repo->createQueryBuilder('l')
            ->leftJoin('l.user', 'u')
            ->leftJoin('l.contentType', 'ct');

        if ($search) {
            $qb->where('l.objectRepr LIKE :search OR l.changeMessage LIKE :search OR u.name LIKE :search OR ct.appLabel LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        $total = (clone $qb)->select('COUNT(l.id)')->getQuery()->getSingleScalarResult();
        $logs = $qb->select('l')
            ->orderBy('l.actionTime', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        return $this->json([
            'logs' => $this->serializer->normalize($logs, null, ['groups' => ['activity_log:read']]),
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'total' => (int) $total,
                'lastPage' => (int) ceil($total / $perPage),
            ],
        ]);
    }
}
