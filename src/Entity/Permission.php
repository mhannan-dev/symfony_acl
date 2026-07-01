<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[ORM\Table(name: 'permissions')]
#[ORM\UniqueConstraint(name: 'unique_content_codename', columns: ['content_type_id', 'codename'])]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'permissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ContentType $contentType = null;

    #[ORM\Column(length: 255)]
    private ?string $codename = null;

    #[ORM\OneToMany(mappedBy: 'permission', targetEntity: GroupPermission::class)]
    private Collection $groupPermissions;

    #[ORM\OneToMany(mappedBy: 'permission', targetEntity: UserPermission::class)]
    private Collection $userPermissions;

    public function __construct()
    {
        $this->groupPermissions = new ArrayCollection();
        $this->userPermissions = new ArrayCollection();
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

    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }

    public function setContentType(?ContentType $contentType): self
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getCodename(): ?string
    {
        return $this->codename;
    }

    public function setCodename(string $codename): self
    {
        $this->codename = $codename;
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
            $groupPermission->setPermission($this);
        }

        return $this;
    }

    public function removeGroupPermission(GroupPermission $groupPermission): self
    {
        if ($this->groupPermissions->removeElement($groupPermission)) {
            if ($groupPermission->getPermission() === $this) {
                $groupPermission->setPermission(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserPermission>
     */
    public function getUserPermissions(): Collection
    {
        return $this->userPermissions;
    }

    public function addUserPermission(UserPermission $userPermission): self
    {
        if (!$this->userPermissions->contains($userPermission)) {
            $this->userPermissions->add($userPermission);
            $userPermission->setPermission($this);
        }

        return $this;
    }

    public function removeUserPermission(UserPermission $userPermission): self
    {
        if ($this->userPermissions->removeElement($userPermission)) {
            if ($userPermission->getPermission() === $this) {
                $userPermission->setPermission(null);
            }
        }

        return $this;
    }
}
