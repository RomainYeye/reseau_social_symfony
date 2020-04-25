<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Publication;
use App\Entity\Commentary;
use App\Controller\Traits\createNewNotificationTrait;
use App\Controller\Traits\createNewNotifyToTrait;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommentaryController extends AbstractController
{

    use createNewNotificationTrait;
    use createNewNotifyToTrait;

    /**
     * @Route("/addCommentary/{id}", name="add_commentary")
     */
    public function addCommentary(Request $request, Publication $publication) {
        $user = $this->getUser();
        $comment = $request->get("comment");

        //appel au Trait
        $notification = $this->createNewNotification($user, $publication, "commentaire");
        //appel au Trait
        $notifyTo = $this->sendNewNotifyToCaseLikeOrCommentary($publication->getUser(), $notification);

        $commentary = (new Commentary())->setUser($user)
                                        ->setPublication($publication)
                                        ->setComment($comment);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($notification);
        $manager->persist($notifyTo);
        $manager->persist($commentary);
        $manager->flush();

        $CommentaryRepo = $this->getDoctrine()->getRepository(Commentary::class);
        $newComment = $CommentaryRepo->find($commentary);

        return new JsonResponse([
            "user" => $newComment->getUser()->getUsername(),
            "comment" => $newComment->getComment()
        ]);
    }
}
