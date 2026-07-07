<?php

namespace App\Entity;

use App\Repository\UserGroupRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserGroupRepository::class)]
#[ORM\Table(name: 'user_groups')]
#[ORM\UniqueConstraint(name: 'unique_user_group', columns: ['user_id', 'group_id'])]
class UserGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userGroups')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read'])]
    private ?Group $group = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;
        return $this;
    }
}
