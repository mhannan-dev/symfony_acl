<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'user:brief', 'activity_log:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:brief', 'activity_log:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['user:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    #[Groups(['user:read'])]
    private bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserGroup::class)]
    #[Groups(['user:read'])]
    private Collection $userGroups;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserPermission::class)]
    private Collection $userPermissions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ActivityLog::class)]
    private Collection $activityLogs;

    public function __construct()
    {
        $this->userGroups = new ArrayCollection();
        $this->userPermissions = new ArrayCollection();
        $this->activityLogs = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
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
            $userGroup->setUser($this);
        }

        return $this;
    }

    public function removeUserGroup(UserGroup $userGroup): self
    {
        if ($this->userGroups->removeElement($userGroup)) {
            if ($userGroup->getUser() === $this) {
                $userGroup->setUser(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        foreach ($this->getUserGroups() as $userGroup) {
            $groupName = $userGroup->getGroup()?->getName();
            if ($groupName) {
                $roles[] = 'ROLE_' . strtoupper($groupName);
            }
        }
        return array_unique($roles);
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
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
            $userPermission->setUser($this);
        }

        return $this;
    }

    public function removeUserPermission(UserPermission $userPermission): self
    {
        if ($this->userPermissions->removeElement($userPermission)) {
            if ($userPermission->getUser() === $this) {
                $userPermission->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ActivityLog>
     */
    public function getActivityLogs(): Collection
    {
        return $this->activityLogs;
    }

    public function addActivityLog(ActivityLog $activityLog): self
    {
        if (!$this->activityLogs->contains($activityLog)) {
            $this->activityLogs->add($activityLog);
            $activityLog->setUser($this);
        }

        return $this;
    }

    public function removeActivityLog(ActivityLog $activityLog): self
    {
        if ($this->activityLogs->removeElement($activityLog)) {
            if ($activityLog->getUser() === $this) {
                $activityLog->setUser(null);
            }
        }

        return $this;
    }
}
