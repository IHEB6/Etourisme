<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;


use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Controller\SecurityController;




class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration (Request $request, ManagerRegistry $managerRegistry, UserPasswordHasherInterface $passwordHasher) {

        $user = new User();
        $em = $managerRegistry->getManager();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

       
        $em->persist($user);
        $em->flush();

           return $this->redirectToRoute('login');
        }

        return $this->render('security/registration.html.twig' , [
            'form' => $form->createView()

        ]);

    }

    /**
     * @Route("/login", name="login")
     */

    public function login (AuthenticationUtils $authenticationUtils){

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

          return $this->render('loginn.html.twig', [
             'last_username' => $lastUsername,
             'error'         => $error,
          ]);

    }

/**
 * @Route("/logout", name="security_logout")
 */
public function logout() {}


}
