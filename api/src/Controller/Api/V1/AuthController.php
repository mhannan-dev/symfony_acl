<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\DTO\LoginRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Controller\Api\ApiResponseTrait;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1')]
class AuthController extends AbstractController
{
    use ApiResponseTrait;

    #[Route('/login', name: 'api_v1_login', methods: ['POST'])]
    public function login(
        LoginRequest $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        Security $security
    ): JsonResponse {
        $user = $userRepository->findOneBy(['email' => $request->email]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $request->password)) {
            return $this->apiError('Invalid email or password.', Response::HTTP_UNAUTHORIZED);
        }

        $security->login($user, 'security.authenticator.form_login.main');

        return $this->apiSuccess([
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ]);
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(): JsonResponse
    {
        return $this->apiSuccess(['message' => 'API is running.']);
    }

    #[Route('/me', name: 'api_v1_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->apiError('Not authenticated.', Response::HTTP_UNAUTHORIZED);
        }

        return $this->apiSuccess([
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ]);
    }

    #[Route('/logout', name: 'api_v1_logout', methods: ['POST'])]
    public function logout(Security $security): JsonResponse
    {
        $security->logout(false);
        return $this->apiSuccess(['message' => 'Logged out successfully.']);
    }
}
