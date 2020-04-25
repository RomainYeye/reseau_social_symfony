<?php

namespace App\Controller;

use App\Repository\NotifyToRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NotifyToController extends AbstractController
{
    private $notifyToRepo;

    public function __construct(NotifyToRepository $notifyToRepo)
    {
        $this->notifyToRepo = $notifyToRepo;
    }

    /**
     * @Route("/notifyto/removePubNotif", name="remove_pub_notif")
     */
    public function removePubNotif(Request $request)
    {   
        $user = $this->getUser();
        $statut = $request->get("statut");
        $myFriendsPubNotifs = $this->notifyToRepo->searchUserNotifsOfType($user, "publication", 0);
        $manager = $this->getDoctrine()->getManager();
        foreach($myFriendsPubNotifs as $notif) {
            $notif->setFlag($statut);
            $manager->persist($notif);
        }
        $manager->flush();

        return new Response(count($this->notifyToRepo->searchUserNotifsOfType($user, "publication", 0)));
    }

    /**
     * @Route("/notifyto/removeLikeNotif", name="remove_like_notif")
     */
    public function removeLikeNotif(Request $request)
    {   
        $user = $this->getUser();
        $statut = $request->get("statut");
        $myFriendsLikeNotifs = $this->notifyToRepo->searchUserNotifsOfType($user, "like", 0);
        $manager = $this->getDoctrine()->getManager();
        foreach($myFriendsLikeNotifs as $notif) {
            $notif->setFlag($statut);
            $manager->persist($notif);
        }
        $manager->flush();

        return new Response(count($this->notifyToRepo->searchUserNotifsOfType($user, "like", 0)));
    }

    /**
     * @Route("/notifyto/removeComNotif", name="remove_com_notif")
     */
    public function removeComNotif(Request $request)
    {   
        $user = $this->getUser();
        $statut = $request->get("statut");
        $myFriendsComNotifs = $this->notifyToRepo->searchUserNotifsOfType($user, "commentaire", 0);
        $manager = $this->getDoctrine()->getManager();
        foreach($myFriendsComNotifs as $notif) {
            $notif->setFlag($statut);
            $manager->persist($notif);
        }
        $manager->flush();

        return new Response(count($this->notifyToRepo->searchUserNotifsOfType($user, "commentaire", 0)));
    }
}
