<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            ['email' => 'admin@yopmail.com', 'password' => 'Test@1234', 'name' => 'Admin'],
            ['email' => 'john@yopmail.com', 'password' => 'Test@1234', 'name' => 'John'],
            ['email' => 'jane@yopmail.com', 'password' => 'Test@1234', 'name' => 'Jane'],
        ];

        foreach ($users as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setName($data['name']);
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                $data['password']
            ));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
