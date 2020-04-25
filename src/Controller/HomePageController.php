<?php

namespace App\Controller;

use DateTime;
use App\Entity\Publication;
use App\Entity\Like;
use App\Form\PublicationType;
use App\Entity\Notification;
use App\Entity\NotifyTo;
use App\Repository\LikeRepository;
use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Traits\createNewNotificationTrait;
use App\Controller\Traits\createNewNotifyToTrait;
use App\Repository\NotifyToRepository;

class HomePageController extends AbstractController
{
    private $publicationRepo;
    private $likeRepo;
    private $notifyToRepo;

    public function __construct(PublicationRepository $publicationRepo, LikeRepository $likeRepo, NotifyToRepository $notifyToRepo)
    {
        $this->publicationRepo = $publicationRepo;
        $this->likeRepo = $likeRepo;
        $this->notifyToRepo = $notifyToRepo;
    }

    use createNewNotificationTrait;
    use createNewNotifyToTrait;

    /**
     * @Route("/homepage", name="home_page")
     * @Route("/", name="home_page_bis")
     */
    
    public function displayHomePage(Request $request)
    {
        $user = $this->getUser();
        
        $myLikeNotifs = $this->notifyToRepo->searchUserNotifsOfType($user, "like", 0);
        $myComNotifs = $this->notifyToRepo->searchUserNotifsOfType($user, "commentaire", 0);
        $myFriendsPubNotifs = $this->notifyToRepo->searchUserNotifsOfType($user, "publication", 0);
        $allMyNewNotifs = $this->notifyToRepo->findBy(["friend" => $this->getUser(), "flag" => 0]);
        
        $arrayOfFriendships = $user->getFriends()->toArray();
        $myFriendsAndMeIds = [];
        foreach ($arrayOfFriendships as $friendship) {
            array_push($myFriendsAndMeIds, $friendship->getFriend()->getId());
        }
        array_push($myFriendsAndMeIds, $user->getId());

        $meAndMyFriendsPublications = $this->publicationRepo->findPublicationsOfPeople($myFriendsAndMeIds);
        $myPublications = $this->publicationRepo->findBy(["user" => $this->getUser()->getId()]);

        $myFriends = [];
        foreach ($arrayOfFriendships as $friendship) {
            array_push($myFriends, $friendship->getFriend());
        }

        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);    
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $manager=$this->getDoctrine()->getManager();

            $notification = $this->createNewNotification($user, $publication, "publication");
            $manager->persist($notification);

            foreach($myFriends as $friend) {
                $notifyTo = $this->sendNewNotifyToCaseNewPublication($friend, $notification);
                $manager->persist($notifyTo);
            }

            $publication->setDate(new DateTime());
            $publication->setUser($user);
            $manager->persist($publication);
            $manager->flush();
            return $this->redirectToRoute('home_page');
        }

        return $this->render('home_page/index.html.twig', [
            'meAndMyFriendsPublications' => $meAndMyFriendsPublications,
            'myPublications' => $myPublications,
            'myFriends' => $myFriends,
            'allMyNewNotifs' => $allMyNewNotifs,
            'formPublication' => $form->createView(),
            'myLikeNotifs' => $myLikeNotifs,
            'myComNotifs' => $myComNotifs,
            'myFriendsPubNotifs' => $myFriendsPubNotifs,
        ]);
    }

     /**
     * @Route("/homepage/like", name="like_publication")
     */

    public function likeAPublication(Request $request)
    {
        $user = $this->getUser();
        $publication = $this->publicationRepo->findOneBy(["id" => $request->get("id-pub")]);

        $like = (new Like())->setUser($user)
                            ->setPublication($publication);

        $notification = $this->createNewNotification($user, $publication, "like");

        $notifyTo = $this->sendNewNotifyToCaseLikeOrCommentary($publication->getUser(), $notification);

        $manager = $this->getDoctrine()->getManager();                           
        $manager->persist($like);
        $manager->persist($notification);
        $manager->persist($notifyTo);
        $manager->flush();

        $likes = count($this->likeRepo->findBy(["publication" => $publication]));

        return new JsonResponse(["likes" => $likes, "newRoute" => "homepage/dislike"]);
        
    }
    
    /**
     * @Route("/homepage/dislike", name="dislike_publication")
     */
    
    public function dislikeAPublication(Request $request)
    {
        $user = $this->getUser();
        $publication = $this->publicationRepo->findOneBy(["id" => $request->get("id-pub")]);

        $likeToRemove = $this->likeRepo->findOneBy([
            "user" => $user,
            "publication" => $publication
        ]);

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($likeToRemove);
        $manager->flush();

        $likes = count($this->likeRepo->findBy(["publication" => $publication]));

        return new JsonResponse(["likes" => $likes, "newRoute" => "homepage/like"]);
        
    }

}
