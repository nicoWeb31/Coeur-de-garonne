<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GlobalController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $user = $this->getUser(); //recuperation du user;
        return $this->render('global/home.html.twig',[
            "user" => $user
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function regiseter(Request $req,EntityManagerInterface $man,UserPasswordEncoderInterface $encode)
    {
        $user = new User();
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($req);
        
        if($form->isSubmitted() && $form->isValid()){
            $passEncode = $encode->encodePassword($user,$user->getPassword());
            $user->setPassword($passEncode);
            $user->setUpdatedAt(new DateTime('now'));
            $user->setRoles("ROLE_USER");
            $man->persist($user);
            $man->flush();
            $this->addFlash("success","Compte crée avec succes");
            return $this->redirectToRoute('login');
        }
        return $this->render('global/register.html.twig',[

            "form"=> $form->createView()

        ]);

    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $utils)
    {
        return $this->render('global/login.html.twig',[
            "lastUserName" => $utils->getLastUsername(),
            "error" => $utils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
    
    }
}
