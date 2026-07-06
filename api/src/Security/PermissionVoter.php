<?php

namespace App\Security;

use App\Entity\User;
use App\Service\PermissionCheckService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PermissionVoter extends Voter
{
    public const VIEW = 'view';
    public const CREATE = 'create';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    private const ACTIONS = [
        self::VIEW => 'view_%s',
        self::CREATE => 'add_%s',
        self::EDIT => 'change_%s',
        self::DELETE => 'delete_%s',
    ];

    public function __construct(
        private readonly PermissionCheckService $permissionCheck,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (isset(self::ACTIONS[$attribute]) && is_string($subject)) {
            return true;
        }

        if (preg_match('/^(view|add|change|delete)_[a-z_]+$/', $attribute)) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (isset(self::ACTIONS[$attribute]) && is_string($subject)) {
            $codename = sprintf(self::ACTIONS[$attribute], $subject);
        } else {
            $codename = $attribute;
        }

        return $this->permissionCheck->hasPermission($user, $codename);
    }
}
