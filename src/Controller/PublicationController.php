<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Publication;
use App\Form\PublicationType;
use App\Repository\PublicationRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class PublicationController extends AbstractController
{
    /**
     * @Route("/publication", name="create_publication")
     */
    public function create(Request $request)
    {
        $user = $this->getUser();
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);    
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $date = new DateTime();
            $publication->setDate($date);
            $user->addPublication($publication);
            $manager=$this->getDoctrine()->getManager();
            $manager->persist($publication);
            $manager->flush();
            return $this->redirectToRoute('home_page');
        }
        return $this->render('publication/index.html.twig', [
            'formPublication' => $form->createView(),
        ]);
    }

    /** 
     * @Route("/publication/{id}/wholike", name="who_like")
     */

    public function whoLikeThePublication(Publication $publication) {
        $likers = [];
        $likes = $publication->getLikes();
        foreach ($likes as $like) {
            array_push($likers, $like->getUser()->getUsername());
        }
        return new JsonResponse($likers);
    }

    /** 
     * @Route("/publication/remove", name="remove_publication")
     */

    public function removePublication(Request $request, PublicationRepository $publicationRepo) {
        $idPublication = $request->get("id-pub");
        $publication = $publicationRepo->findOneBy(["id" => $idPublication]);
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($publication);
        $manager->flush();
        return new Response("done");
    }

}
