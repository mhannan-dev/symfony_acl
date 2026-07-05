<?php

namespace App\Controller;

use App\DTO\LoginRequest;
use App\Repository\UserRepository;
use LogicException;
use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['GET'])]
    public function login(AuthenticationUtils $authenticationUtils, InertiaInterface $inertia, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $errors = (object)[];
        if ($request && $request->hasSession() && $request->getSession()->getFlashBag()->has('inertia_errors')) {
            $flashed = $request->getSession()->getFlashBag()->get('inertia_errors')[0] ?? [];
            if (!empty($flashed)) {
                $errors = $flashed;
            }
        }

        return $inertia->render('Login', [
            'email' => $authenticationUtils->getLastUsername(),
            'errors' => $errors,
        ]);
    }

    #[Route(path: '/login', name: 'app_login_post', methods: ['POST'])]
    public function loginPost(
        LoginRequest $loginRequest,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        Security $security,
        Request $request
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = $userRepository->findOneBy(['email' => $loginRequest->email]);
        
        if (!$user || !$passwordHasher->isPasswordValid($user, $loginRequest->password)) {
            // Flash the custom validation error
            $request->getSession()->getFlashBag()->set('inertia_errors', [
                'email' => 'The email address or password you entered is incorrect.',
            ]);
            
            return $this->redirectToRoute('app_login');
        }

        // Manually authenticate the user
        $security->login($user, 'security.authenticator.form_login.main');
        
        return $this->redirectToRoute('app_home');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
