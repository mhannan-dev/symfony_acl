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
        if (!isset(self::ACTIONS[$attribute])) {
            return false;
        }

        if ($subject !== null && !is_string($subject)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $codename = sprintf(self::ACTIONS[$attribute], $subject);

        return $this->permissionCheck->hasPermission($user, $codename);
    }
}
