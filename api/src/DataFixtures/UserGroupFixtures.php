<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\User;
use App\Entity\UserGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserGroupFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [UserFixtures::class, GroupFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $assignments = [
            ['userRef' => UserFixtures::USER_ADMIN, 'groupRef' => GroupFixtures::GROUP_ADMIN],
            ['userRef' => UserFixtures::USER_JOHN, 'groupRef' => GroupFixtures::GROUP_EDITOR],
            ['userRef' => UserFixtures::USER_JANE, 'groupRef' => GroupFixtures::GROUP_VIEWER],
        ];

        foreach ($assignments as $data) {
            $ug = new UserGroup();
            $ug->setUser($this->getReference($data['userRef'], User::class));
            $ug->setGroup($this->getReference($data['groupRef'], Group::class));
            $manager->persist($ug);
        }

        $manager->flush();
    }
}
