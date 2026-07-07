<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const string USER_ADMIN = 'user_admin';
    public const string USER_JOHN = 'user_john';
    public const string USER_JANE = 'user_jane';

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            ['email' => 'admin@yopmail.com', 'password' => 'Test@1234', 'name' => 'Admin', 'ref' => self::USER_ADMIN],
            ['email' => 'john@yopmail.com', 'password' => 'Test@1234', 'name' => 'John', 'ref' => self::USER_JOHN],
            ['email' => 'jane@yopmail.com', 'password' => 'Test@1234', 'name' => 'Jane', 'ref' => self::USER_JANE],
        ];

        foreach ($users as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setName($data['name']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
            $user->setIsActive($data['isActive'] ?? true);
            $manager->persist($user);
            $this->addReference($data['ref'], $user);
        }

        $manager->flush();
    }
}
