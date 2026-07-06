<?php

namespace App\DataFixtures;

use App\Entity\ContentType;
use App\Entity\Permission;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PermissionFixtures extends Fixture implements DependentFixtureInterface
{
    public const string PERMISSION_USER_ADD = 'permission_user_add';
    public const string PERMISSION_USER_CHANGE = 'permission_user_change';
    public const string PERMISSION_USER_DELETE = 'permission_user_delete';
    public const string PERMISSION_USER_VIEW = 'permission_user_view';
    public const string PERMISSION_GROUP_ADD = 'permission_group_add';
    public const string PERMISSION_GROUP_CHANGE = 'permission_group_change';
    public const string PERMISSION_GROUP_DELETE = 'permission_group_delete';
    public const string PERMISSION_GROUP_VIEW = 'permission_group_view';
    public const string PERMISSION_PERMISSION_ADD = 'permission_permission_add';
    public const string PERMISSION_PERMISSION_CHANGE = 'permission_permission_change';
    public const string PERMISSION_PERMISSION_DELETE = 'permission_permission_delete';
    public const string PERMISSION_PERMISSION_VIEW = 'permission_permission_view';
    public const string PERMISSION_CONTENT_TYPE_ADD = 'permission_content_type_add';
    public const string PERMISSION_CONTENT_TYPE_CHANGE = 'permission_content_type_change';
    public const string PERMISSION_CONTENT_TYPE_DELETE = 'permission_content_type_delete';
    public const string PERMISSION_CONTENT_TYPE_VIEW = 'permission_content_type_view';
    public const string PERMISSION_ACTIVITY_LOG_ADD = 'permission_activity_log_add';
    public const string PERMISSION_ACTIVITY_LOG_CHANGE = 'permission_activity_log_change';
    public const string PERMISSION_ACTIVITY_LOG_DELETE = 'permission_activity_log_delete';
    public const string PERMISSION_ACTIVITY_LOG_VIEW = 'permission_activity_log_view';

    public function getDependencies(): array
    {
        return [ContentTypeFixtures::class];
    }

    private function createPermission(ObjectManager $manager, ContentType $ct, string $codename): Permission
    {
        $p = new Permission();
        $p->setContentType($ct);
        $p->setCodename($codename);
        $p->setName(ucfirst($codename) . ' ' . ucfirst($ct->getModel()));
        return $p;
    }

    public function load(ObjectManager $manager): void
    {
        $contentTypes = [
            ContentTypeFixtures::CONTENT_TYPE_USER => [
                'add_user' => self::PERMISSION_USER_ADD,
                'change_user' => self::PERMISSION_USER_CHANGE,
                'delete_user' => self::PERMISSION_USER_DELETE,
                'view_user' => self::PERMISSION_USER_VIEW,
            ],
            ContentTypeFixtures::CONTENT_TYPE_GROUP => [
                'add_group' => self::PERMISSION_GROUP_ADD,
                'change_group' => self::PERMISSION_GROUP_CHANGE,
                'delete_group' => self::PERMISSION_GROUP_DELETE,
                'view_group' => self::PERMISSION_GROUP_VIEW,
            ],
            ContentTypeFixtures::CONTENT_TYPE_PERMISSION => [
                'add_permission' => self::PERMISSION_PERMISSION_ADD,
                'change_permission' => self::PERMISSION_PERMISSION_CHANGE,
                'delete_permission' => self::PERMISSION_PERMISSION_DELETE,
                'view_permission' => self::PERMISSION_PERMISSION_VIEW,
            ],
            ContentTypeFixtures::CONTENT_TYPE_CONTENT_TYPE => [
                'add_content_type' => self::PERMISSION_CONTENT_TYPE_ADD,
                'change_content_type' => self::PERMISSION_CONTENT_TYPE_CHANGE,
                'delete_content_type' => self::PERMISSION_CONTENT_TYPE_DELETE,
                'view_content_type' => self::PERMISSION_CONTENT_TYPE_VIEW,
            ],
            ContentTypeFixtures::CONTENT_TYPE_ACTIVITY_LOG => [
                'add_activity_log' => self::PERMISSION_ACTIVITY_LOG_ADD,
                'change_activity_log' => self::PERMISSION_ACTIVITY_LOG_CHANGE,
                'delete_activity_log' => self::PERMISSION_ACTIVITY_LOG_DELETE,
                'view_activity_log' => self::PERMISSION_ACTIVITY_LOG_VIEW,
            ],
        ];

        foreach ($contentTypes as $ctRef => $perms) {
            $ct = $this->getReference($ctRef, ContentType::class);
            foreach ($perms as $codename => $ref) {
                $p = $this->createPermission($manager, $ct, $codename);
                $manager->persist($p);
                $this->addReference($ref, $p);
            }
        }

        $manager->flush();
    }
}
