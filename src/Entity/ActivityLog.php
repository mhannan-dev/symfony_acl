<?php

namespace App\Entity;

use App\Repository\ActivityLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivityLogRepository::class)]
#[ORM\Table(name: 'activity_logs')]
class ActivityLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $actionTime = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $objectId = null;

    #[ORM\Column(length: 255)]
    private ?string $objectRepr = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $actionFlag = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $changeMessage = null;

    #[ORM\ManyToOne(inversedBy: 'activityLogs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'activityLogs')]
    private ?ContentType $contentType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActionTime(): ?\DateTimeInterface
    {
        return $this->actionTime;
    }

    public function setActionTime(\DateTimeInterface $actionTime): self
    {
        $this->actionTime = $actionTime;
        return $this;
    }

    public function getObjectId(): ?string
    {
        return $this->objectId;
    }

    public function setObjectId(?string $objectId): self
    {
        $this->objectId = $objectId;
        return $this;
    }

    public function getObjectRepr(): ?string
    {
        return $this->objectRepr;
    }

    public function setObjectRepr(string $objectRepr): self
    {
        $this->objectRepr = $objectRepr;
        return $this;
    }

    public function getActionFlag(): ?int
    {
        return $this->actionFlag;
    }

    public function setActionFlag(int $actionFlag): self
    {
        $this->actionFlag = $actionFlag;
        return $this;
    }

    public function getChangeMessage(): ?string
    {
        return $this->changeMessage;
    }

    public function setChangeMessage(string $changeMessage): self
    {
        $this->changeMessage = $changeMessage;
        return $this;
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

    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }

    public function setContentType(?ContentType $contentType): self
    {
        $this->contentType = $contentType;
        return $this;
    }
}
