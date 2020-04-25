<?php

namespace App\Controller\Traits;

use App\Entity\User;
use App\Entity\Publication;
use App\Entity\Notification;
use App\Entity\NotifyTo;

trait createNewNotifyToTrait {

    public function sendNewNotifyToCaseLikeOrCommentary(User $publicationAuthor, Notification $notification) : NotifyTo
    {
        $notifyTo = new NotifyTo();
        $notifyTo->setFriend($publicationAuthor)
                 ->setNotification($notification);

        if($notification->getUser() === $notification->getPublication()->getUser()) {
            $notifyTo->setFlag(1);
        } 
        else {
            $notifyTo->setFlag(0);
        }

        return $notifyTo;
    }

    public function sendNewNotifyToCaseNewPublication(User $myFriend, Notification $notification) : NotifyTo
    {
        $notifyTo = (new NotifyTo())->setFriend($myFriend)
                                    ->setNotification($notification)
                                    ->setFlag(0);

        return $notifyTo;
    }
}

?>