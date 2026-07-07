<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Controller\Api\ApiResponseTrait;

#[Route('/api/v1/users')]
class UserController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('', name: 'api_v1_users_list', methods: ['GET'])]
    #[IsGranted('view_user')]
    public function index(Request $request, UserRepository $repo): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $perPage = min(100, max(1, $request->query->getInt('perPage', 10)));
        $search = $request->query->getString('search', '');

        $qb = $repo->createQueryBuilder('u');

        if ($search) {
            $qb->where('u.name LIKE :search OR u.email LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        $total = (clone $qb)->select('COUNT(u.id)')->getQuery()->getSingleScalarResult();
        $users = $qb->select('u')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        return $this->apiSuccess([
            'users' => $this->serializer->normalize($users, null, ['groups' => ['user:read']]),
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'total' => (int) $total,
                'lastPage' => (int) ceil($total / $perPage),
            ],
        ]);
    }

    #[Route('/new', name: 'api_v1_users_new_form', methods: ['GET'])]
    #[IsGranted('add_user')]
    public function new(GroupRepository $groupRepo): JsonResponse
    {
        $groups = $groupRepo->findAll();

        return $this->apiSuccess([
            'groups' => $this->serializer->normalize($groups, null, ['groups' => ['group:brief']]),
            'user' => null,
            'userGroupIds' => [],
        ]);
    }

    #[Route('/save', name: 'api_v1_users_save', methods: ['POST'])]
    public function save(Request $request, GroupRepository $groupRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->denyAccessUnlessGranted(empty($data['id']) ? 'create' : 'edit', 'user');

        $user = $data['id'] ? $this->em->getRepository(User::class)->find($data['id']) : new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);

        if (!empty($data['password'])) {
            $user->setPassword($this->hasher->hashPassword($user, $data['password']));
        }

        foreach ($user->getUserGroups()->toArray() as $ug) {
            $this->em->remove($ug);
        }
        $user->getUserGroups()->clear();
        $this->em->flush();

        $selectedGroupIds = $data['groupIds'] ?? [];
        foreach ($selectedGroupIds as $groupId) {
            $group = $groupRepo->find($groupId);
            if ($group) {
                $ug = new UserGroup();
                $ug->setUser($user);
                $ug->setGroup($group);
                $this->em->persist($ug);
                $user->addUserGroup($ug);
            }
        }

        $this->em->persist($user);
        $this->em->flush();

        return $this->apiSuccess([
            'user' => $this->serializer->normalize($user, null, ['groups' => ['user:brief']]),
        ]);
    }

    #[Route('/{id}/edit', name: 'api_v1_users_edit_form', methods: ['GET'])]
    #[IsGranted('change_user')]
    public function edit(User $user, GroupRepository $groupRepo): JsonResponse
    {
        $groups = $groupRepo->findAll();
        $userGroupIds = array_map(fn($ug) => $ug->getGroup()->getId(), $user->getUserGroups()->toArray());

        return $this->apiSuccess([
            'user' => $this->serializer->normalize($user, null, ['groups' => ['user:read']]),
            'groups' => $this->serializer->normalize($groups, null, ['groups' => ['group:brief']]),
            'userGroupIds' => $userGroupIds,
        ]);
    }

    #[Route('/{id}/toggle-status', name: 'api_v1_users_toggle_status', methods: ['PATCH'])]
    #[IsGranted('change_user')]
    public function toggleStatus(User $user): JsonResponse
    {
        $user->setIsActive(!$user->isActive());
        $this->em->flush();

        return $this->apiSuccess([
            'id' => $user->getId(),
            'isActive' => $user->isActive(),
        ]);
    }

    #[Route('/{id}/delete', name: 'api_v1_users_delete', methods: ['DELETE'])]
    #[IsGranted('delete_user')]
    public function delete(User $user): JsonResponse
    {
        $this->em->remove($user);
        $this->em->flush();

        return $this->apiSuccess(['message' => 'User deleted.']);
    }
}
