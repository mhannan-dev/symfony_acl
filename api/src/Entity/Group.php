<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: 'groups')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['group:read', 'group:brief', 'user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['group:read', 'group:brief', 'user:read'])]
    private ?string $name = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    #[Groups(['group:read'])]
    private bool $status = true;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

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

    public function isStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

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
    #[Groups(['group:read'])]
    #[SerializedName('groupPermissions')]
    public function getPermissionIds(): array
    {
        return $this->groupPermissions->map(static fn (GroupPermission $gp) => $gp->getPermission()->getId())->toArray();
    }

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
