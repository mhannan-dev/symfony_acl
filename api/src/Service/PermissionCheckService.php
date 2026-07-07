<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PermissionCheckService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CacheInterface $cache,
    ) {
    }

    public function hasPermission(User $user, string $codename): bool
    {
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        $cacheKey = sprintf('user_permissions_%d', $user->getId());

        $permissions = $this->cache->get($cacheKey, function (ItemInterface $item) use ($user) {
            $item->expiresAfter(3600);
            return $this->getUserPermissions($user);
        });

        foreach ($permissions as $permission) {
            if ($permission['codename'] === $codename) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyPermission(User $user, string ...$codenames): bool
    {
        foreach ($codenames as $codename) {
            if ($this->hasPermission($user, $codename)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions(User $user, string ...$codenames): bool
    {
        foreach ($codenames as $codename) {
            if (!$this->hasPermission($user, $codename)) {
                return false;
            }
        }
        return true;
    }

    public function getUserPermissions(User $user): array
    {
        $conn = $this->em->getConnection();

        $direct = $conn->fetchAllAssociative(
            'SELECT p.id, p.name, p.codename, p.content_type_id, \'direct\' as source
             FROM user_permissions up
             JOIN permissions p ON p.id = up.permission_id
             WHERE up.user_id = :userId',
            ['userId' => $user->getId()]
        );

        $group = $conn->fetchAllAssociative(
            'SELECT DISTINCT p.id, p.name, p.codename, p.content_type_id, \'group\' as source
             FROM user_groups ug
             JOIN group_permissions gp ON gp.group_id = ug.group_id
             JOIN permissions p ON p.id = gp.permission_id
             WHERE ug.user_id = :userId',
            ['userId' => $user->getId()]
        );

        return array_merge($direct, $group);
    }
}
