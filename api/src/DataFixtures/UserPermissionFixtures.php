<?php

namespace App\DataFixtures;

use App\Entity\Permission;
use App\Entity\User;
use App\Entity\UserPermission;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserPermissionFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [UserFixtures::class, PermissionFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $assignments = [
            UserFixtures::USER_ADMIN => [
                PermissionFixtures::PERMISSION_USER_DELETE,
            ],
        ];

        foreach ($assignments as $userRef => $permRefs) {
            $user = $this->getReference($userRef, User::class);
            foreach ($permRefs as $permRef) {
                $up = new UserPermission();
                $up->setUser($user);
                $up->setPermission($this->getReference($permRef, Permission::class));
                $manager->persist($up);
            }
        }

        $manager->flush();
    }
}
