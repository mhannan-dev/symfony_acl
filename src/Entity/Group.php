<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: 'groups')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'group', targetEntity: GroupPermission::class)]
    private Collection $groupPermissions;

    #[ORM\OneToMany(mappedBy: 'group', targetEntity: UserGroup::class)]
    private Collection $userGroups;

    public function __construct()
    {
        $this->groupPermissions = new ArrayCollection();
        $this->userGroups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, GroupPermission>
     */
    public function getGroupPermissions(): Collection
    {
        return $this->groupPermissions;
    }

    public function addGroupPermission(GroupPermission $groupPermission): self
    {
        if (!$this->groupPermissions->contains($groupPermission)) {
            $this->groupPermissions->add($groupPermission);
            $groupPermission->setGroup($this);
        }

        return $this;
    }

    public function removeGroupPermission(GroupPermission $groupPermission): self
    {
        if ($this->groupPermissions->removeElement($groupPermission)) {
            if ($groupPermission->getGroup() === $this) {
                $groupPermission->setGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserGroup>
     */
    public function getUserGroups(): Collection
    {
        return $this->userGroups;
    }

    public function addUserGroup(UserGroup $userGroup): self
    {
        if (!$this->userGroups->contains($userGroup)) {
            $this->userGroups->add($userGroup);
            $userGroup->setGroup($this);
        }

        return $this;
    }

    public function removeUserGroup(UserGroup $userGroup): self
    {
        if ($this->userGroups->removeElement($userGroup)) {
            if ($userGroup->getGroup() === $this) {
                $userGroup->setGroup(null);
            }
        }

        return $this;
    }
}
