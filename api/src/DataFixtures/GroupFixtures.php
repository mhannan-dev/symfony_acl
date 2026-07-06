<?php

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixtures extends Fixture
{
    public const string GROUP_ADMIN = 'group_admin';
    public const string GROUP_EDITOR = 'group_editor';
    public const string GROUP_VIEWER = 'group_viewer';

    public function load(ObjectManager $manager): void
    {
        $groups = [
            ['name' => 'Admin', 'ref' => self::GROUP_ADMIN],
            ['name' => 'Editor', 'ref' => self::GROUP_EDITOR],
            ['name' => 'Viewer', 'ref' => self::GROUP_VIEWER],
        ];

        foreach ($groups as $data) {
            $group = new Group();
            $group->setName($data['name']);
            $manager->persist($group);
            $this->addReference($data['ref'], $group);
        }

        $manager->flush();
    }
}
