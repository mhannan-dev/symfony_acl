<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\DTO\LoginRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\PermissionCheckService;
use App\Controller\Api\ApiResponseTrait;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1')]
class AuthController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(
        private readonly SerializerInterface $serializer
    ) {
    }

    #[Route('/login', name: 'api_v1_login', methods: ['POST'])]
    public function login(
        LoginRequest $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        Security $security,
        PermissionCheckService $permissionCheckService
    ): JsonResponse {
        $user = $userRepository->findOneBy(['email' => $request->email]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $request->password)) {
            return $this->apiError('Invalid email or password.', Response::HTTP_UNAUTHORIZED);
        }

        $security->login($user, 'security.authenticator.form_login.main');

        $permissions = array_values(array_filter(array_map(
            fn($p) => $p['codename'] ?? null,
            $permissionCheckService->getUserPermissions($user)
        )));

        $userData = $this->serializer->normalize($user, null, ['groups' => ['user:read']]);
        $userData['roles'] = $user->getRoles();
        $userData['permissions'] = $permissions;

        return $this->apiSuccess([
            'user' => $userData,
        ]);
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(): JsonResponse
    {
        return $this->apiSuccess(['message' => 'API is running.']);
    }

    #[Route('/me', name: 'api_v1_me', methods: ['GET'])]
    public function me(PermissionCheckService $permissionCheckService): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->apiError('Not authenticated.', Response::HTTP_UNAUTHORIZED);
        }

        $permissions = array_values(array_filter(array_map(
            fn($p) => $p['codename'] ?? null,
            $permissionCheckService->getUserPermissions($user)
        )));

        $userData = $this->serializer->normalize($user, null, ['groups' => ['user:read']]);
        $userData['roles'] = $user->getRoles();
        $userData['permissions'] = $permissions;

        return $this->apiSuccess([
            'user' => $userData,
        ]);
    }

    #[Route('/profile', name: 'api_v1_profile_update', methods: ['POST'])]
    public function updateProfile(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        PermissionCheckService $permissionCheckService
    ): JsonResponse {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->apiError('Not authenticated.', Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (!empty($data['name'])) {
            $user->setName($data['name']);
        }

        if (!empty($data['email'])) {
            // In a real app, you might want to check for duplicate emails here
            $user->setEmail($data['email']);
        }

        if (!empty($data['password'])) {
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        }

        $em->flush();

        $permissions = array_values(array_filter(array_map(
            fn($p) => $p['codename'] ?? null,
            $permissionCheckService->getUserPermissions($user)
        )));

        $userData = $this->serializer->normalize($user, null, ['groups' => ['user:read']]);
        $userData['roles'] = $user->getRoles();
        $userData['permissions'] = $permissions;

        return $this->apiSuccess([
            'user' => $userData,
            'message' => 'Profile updated successfully.'
        ]);
    }

    #[Route('/logout', name: 'api_v1_logout', methods: ['POST'])]
    public function logout(Security $security): JsonResponse
    {
        $security->logout(false);
        return $this->apiSuccess(['message' => 'Logged out successfully.']);
    }
}
