<?php

namespace App\Controller\Traits;

use App\Entity\User;
use App\Entity\Publication;
use App\Entity\Notification;
use DateTime;

trait createNewNotificationTrait {
    
    public function createNewNotification(User $userWhoGeneratesNotif, Publication $publication, string $notifType) : Notification
    {
        $notification = (new Notification())->setUser($userWhoGeneratesNotif)
                            ->setPublication($publication)
                            ->setType($notifType)
                            ->setDate(new Datetime());

        return $notification;
    }
}

?>