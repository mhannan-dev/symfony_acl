<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\GroupPermission;
use App\Entity\Permission;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GroupPermissionFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [GroupFixtures::class, PermissionFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $assignments = [
            GroupFixtures::GROUP_ADMIN => [
                PermissionFixtures::PERMISSION_USER_ADD,
                PermissionFixtures::PERMISSION_USER_CHANGE,
                PermissionFixtures::PERMISSION_USER_DELETE,
                PermissionFixtures::PERMISSION_USER_VIEW,
                PermissionFixtures::PERMISSION_GROUP_ADD,
                PermissionFixtures::PERMISSION_GROUP_CHANGE,
                PermissionFixtures::PERMISSION_GROUP_DELETE,
                PermissionFixtures::PERMISSION_GROUP_VIEW,
                PermissionFixtures::PERMISSION_PERMISSION_ADD,
                PermissionFixtures::PERMISSION_PERMISSION_CHANGE,
                PermissionFixtures::PERMISSION_PERMISSION_DELETE,
                PermissionFixtures::PERMISSION_PERMISSION_VIEW,
                PermissionFixtures::PERMISSION_CONTENT_TYPE_ADD,
                PermissionFixtures::PERMISSION_CONTENT_TYPE_CHANGE,
                PermissionFixtures::PERMISSION_CONTENT_TYPE_DELETE,
                PermissionFixtures::PERMISSION_CONTENT_TYPE_VIEW,
                PermissionFixtures::PERMISSION_ACTIVITY_LOG_ADD,
                PermissionFixtures::PERMISSION_ACTIVITY_LOG_CHANGE,
                PermissionFixtures::PERMISSION_ACTIVITY_LOG_DELETE,
                PermissionFixtures::PERMISSION_ACTIVITY_LOG_VIEW,
            ],
            GroupFixtures::GROUP_EDITOR => [
                PermissionFixtures::PERMISSION_USER_ADD,
                PermissionFixtures::PERMISSION_USER_CHANGE,
                PermissionFixtures::PERMISSION_USER_VIEW,
                PermissionFixtures::PERMISSION_GROUP_VIEW,
                PermissionFixtures::PERMISSION_PERMISSION_VIEW,
                PermissionFixtures::PERMISSION_CONTENT_TYPE_VIEW,
                PermissionFixtures::PERMISSION_ACTIVITY_LOG_ADD,
                PermissionFixtures::PERMISSION_ACTIVITY_LOG_VIEW,
            ],
            GroupFixtures::GROUP_VIEWER => [
                PermissionFixtures::PERMISSION_USER_VIEW,
                PermissionFixtures::PERMISSION_GROUP_VIEW,
                PermissionFixtures::PERMISSION_PERMISSION_VIEW,
                PermissionFixtures::PERMISSION_CONTENT_TYPE_VIEW,
                PermissionFixtures::PERMISSION_ACTIVITY_LOG_VIEW,
            ],
        ];

        foreach ($assignments as $groupRef => $permRefs) {
            $group = $this->getReference($groupRef, Group::class);
            foreach ($permRefs as $permRef) {
                $gp = new GroupPermission();
                $gp->setGroup($group);
                $gp->setPermission($this->getReference($permRef, Permission::class));
                $manager->persist($gp);
            }
        }

        $manager->flush();
    }
}
