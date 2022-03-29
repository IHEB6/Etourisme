<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use App\Services\UserService;
use App\Form\RegistrationType;
use App\Form\EditUserPasswordType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);


    }

/**
 * @Route("/utilisateurs", name="utilisateurs")
 */
public function usersList(UserRepository $users)
{

    return $this->render('admin/users.html.twig', [
        'users' => $users->findAll(),
    ]);

}


/**
 * @Route("/utilisateurs/modifier/{id}", name="modifier_utilisateur")
 */
public function editUser(User $user, Request $request, UserPasswordHasherInterface $passwordHasher)
{
    
    $entityManager = $this->getDoctrine()->getManager();

    $form = $this->createForm(EditUserType::class, $user);
    $form->handleRequest($request);

    
    $formpassword = $this->createForm(EditUserPasswordType::class, $user);
    $formpassword->handleRequest($request);


    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('message', 'Utilisateur modifié avec succès');
        return $this->redirectToRoute('admin_utilisateurs');
    }

    
    if ($formpassword->isSubmitted() && $formpassword->isValid()) {
        
        $form->handleRequest($request);
       // dd($user->getPassword());
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );
        $user->setPassword($hashedPassword);


        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('message', 'mot de passe Utilisateur modifié avec succès');
        return $this->redirectToRoute('admin_utilisateurs');
    }
    
    return $this->render('admin/edituser.html.twig', [
        'userForm' => $form->createView(),'formpassword' => $formpassword->createView(),
    ]);

    
}




/**
 * @Route("/utilisateurs/delete/{id}", name="delete")
 */
public function delete($id)
{
    $data = $this->getDoctrine()->getRepository(User::class)->find($id);
    $em = $this->getDoctrine()->getManager();
    $em->remove($data);
    $em->flush();

        $this->addFlash('message', 'Utilisateur supprimé');
        return $this->redirectToRoute('admin_utilisateurs');
    }



     /**
     * @Route("/utilisateurs/add", name="add")
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
            $roles[]='ROLE_USER';
            $roles[]='ROLE_ADMIN';
            $user->setRoles($roles);
            $em->persist($user);
            $em->flush();

           return $this->redirectToRoute('admin_utilisateurs');
        }

        return $this->render('admin/adduser.html.twig' , [
            'form' => $form->createView()

        ]);

    }


    
     /**
     * @Route("/utilisateurs/profile", name="modif")
     */


    public function editAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             $user = $this->UserService->persist($user);
             $request->getSession()->getFlashBag()->add('success', 'modification avec succée !');
             return $this->redirectToRoute('admin_utilisateurs');
        }

        return $this->render('admin/adduser.html.twig', array('form' => $form->createView(),'ligne' => $user));
    } 


    
    

/**
 * @Route("/deconnexion", name="security_logout")
 */
public function logout() {}


    
}



    

