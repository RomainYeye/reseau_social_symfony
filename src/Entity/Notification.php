<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 */
class Notification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Publication", inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $publication;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NotifyTo", mappedBy="notification", cascade={"persist"}, orphanRemoval=true)
     */
    private $notifyTos;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    public function __construct()
    {
        $this->notifyTos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getPublication(): ?Publication
    {
        return $this->publication;
    }

    public function setPublication(?Publication $publication): self
    {
        $this->publication = $publication;

        return $this;
    }

    /**
     * @return Collection|NotifyTo[]
     */
    public function getNotifyTos(): Collection
    {
        return $this->notifyTos;
    }

    public function addNotifyTo(NotifyTo $notifyTo): self
    {
        if (!$this->notifyTos->contains($notifyTo)) {
            $this->notifyTos[] = $notifyTo;
            $notifyTo->setNotification($this);
        }

        return $this;
    }

    public function removeNotifyTo(NotifyTo $notifyTo): self
    {
        if ($this->notifyTos->contains($notifyTo)) {
            $this->notifyTos->removeElement($notifyTo);
            // set the owning side to null (unless already changed)
            if ($notifyTo->getNotification() === $this) {
                $notifyTo->setNotification(null);
            }
        }

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
