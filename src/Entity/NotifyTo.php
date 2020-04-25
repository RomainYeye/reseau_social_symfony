<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotifyToRepository")
 */
class NotifyTo
{

    /**
     * @ORM\Column(type="boolean")
     */
    private $flag;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Notification", inversedBy="notifyTos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $notification;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notifyTos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $friend;

    public function getFlag(): ?bool
    {
        return $this->flag;
    }

    public function setFlag(bool $flag): self
    {
        $this->flag = $flag;

        return $this;
    }

    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    public function setNotification(?Notification $notification): self
    {
        $this->notification = $notification;

        return $this;
    }

    public function getFriend(): ?User
    {
        return $this->friend;
    }

    public function setFriend(?User $friend): self
    {
        $this->friend = $friend;

        return $this;
    }

}
