<?php

namespace App\Entity;

use App\Repository\ContentTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity(repositoryClass: ContentTypeRepository::class)]
#[ORM\Table(name: 'content_types')]
class ContentType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['content_type:read', 'content_type:brief', 'permission:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['content_type:read', 'content_type:brief', 'permission:read', 'activity_log:read'])]
    private ?string $appLabel = null;

    #[ORM\Column(length: 255)]
    #[Groups(['content_type:read', 'content_type:brief', 'permission:read'])]
    private ?string $model = null;

    #[ORM\OneToMany(mappedBy: 'contentType', targetEntity: Permission::class)]
    private Collection $permissions;

    #[ORM\OneToMany(mappedBy: 'contentType', targetEntity: ActivityLog::class)]
    private Collection $activityLogs;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->activityLogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAppLabel(): ?string
    {
        return $this->appLabel;
    }

    public function setAppLabel(string $appLabel): self
    {
        $this->appLabel = $appLabel;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return Collection<int, Permission>
     */
    #[Groups(['content_type:read'])]
    #[SerializedName('permissions')]
    public function getPermissionIds(): array
    {
        return $this->permissions->map(static fn (Permission $p) => $p->getId())->toArray();
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
            $permission->setContentType($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->removeElement($permission)) {
            if ($permission->getContentType() === $this) {
                $permission->setContentType(null);
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
            $activityLog->setContentType($this);
        }

        return $this;
    }

    public function removeActivityLog(ActivityLog $activityLog): self
    {
        if ($this->activityLogs->removeElement($activityLog)) {
            if ($activityLog->getContentType() === $this) {
                $activityLog->setContentType(null);
            }
        }

        return $this;
    }
}
