<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{
    /**
     * @Route("/register", name="register_route")
     * 
     * @return void
     */
    public function create(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        if ($this->getUser()) {
            return $this->redirectToRoute('home_page');
        }
        
        $user = new User();
        $form = $this->createForm(UserType::class, $user);    
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $manager=$this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/form-user.html.twig', [
            'formUser' => $form->createView()
        ]);
    }

    


}
