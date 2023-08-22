<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AccountPasswordController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/account/password", name="app_account_password")
     */
    public function index(Request $request, UserPasswordHasherInterface $encoder): Response
    {

        $notifictaion = null;
        $user = $this->getUser();
        $form = $this->createForm( ChangePasswordType::class, $user);

        $form->handleRequest($request);

        $old_pwd = $form->get('old_password')->getData();

        if($form->isSubmitted() && $form->isValid()){
            if($encoder->isPasswordValid($user, $old_pwd)){
               $new_pwd = $form->get('new_password')->getData();
               $encodedPassword = $encoder->hashPassword($user, $new_pwd);
               $user->setPassword($encodedPassword);
               $this->entityManager->persist($user);
               $this->entityManager->flush();
               $notifictaion = "Votre mot de passe à bien été mis à jour";
            }else{
               $notifictaion = "Votre mot de passe actuel n'est pas le bon";
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notifictaion
        ]);
    }
}
