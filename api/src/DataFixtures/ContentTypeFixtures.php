<?php

namespace App\DataFixtures;

use App\Entity\ContentType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ContentTypeFixtures extends Fixture
{
    public const string CONTENT_TYPE_USER = 'content_type_user';
    public const string CONTENT_TYPE_GROUP = 'content_type_group';
    public const string CONTENT_TYPE_PERMISSION = 'content_type_permission';
    public const string CONTENT_TYPE_CONTENT_TYPE = 'content_type_content_type';
    public const string CONTENT_TYPE_ACTIVITY_LOG = 'content_type_activity_log';

    public function load(ObjectManager $manager): void
    {
        $types = [
            ['appLabel' => 'app', 'model' => 'user', 'ref' => self::CONTENT_TYPE_USER],
            ['appLabel' => 'app', 'model' => 'group', 'ref' => self::CONTENT_TYPE_GROUP],
            ['appLabel' => 'app', 'model' => 'permission', 'ref' => self::CONTENT_TYPE_PERMISSION],
            ['appLabel' => 'app', 'model' => 'contenttype', 'ref' => self::CONTENT_TYPE_CONTENT_TYPE],
            ['appLabel' => 'app', 'model' => 'activitylog', 'ref' => self::CONTENT_TYPE_ACTIVITY_LOG],
        ];

        foreach ($types as $data) {
            $ct = new ContentType();
            $ct->setAppLabel($data['appLabel']);
            $ct->setModel($data['model']);
            $manager->persist($ct);
            $this->addReference($data['ref'], $ct);
        }

        $manager->flush();
    }
}
