<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressControlerController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/compte/addresses", name="app_account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig', [
        ]);
    }

    /**
     * @Route("/compte/ajouter-une-addresse", name="app_account_add_address")
     */
    public function add(Cart $cart,Request $request): Response
    {
        $address = new Address();

        $form = $this->createForm( AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
              $address->setUser($this->getUser());
              $this->entityManager->persist($address);
              $this->entityManager->flush();

              if($cart->get()){
                   return $this->redirectToRoute('order');
              }else{
                   return $this->redirectToRoute('app_account_address');
              }

              return $this->redirectToRoute('app_account_address');

        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/modifier-une-addresse/{id}", name="app_account_edit_address")
     */
    public function edit(Request $request, $id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if(!$address || $address->getUser() != $this->getUser()){
            return $this->redirectToRoute('app_account_address');
        }
        
        $form = $this->createForm( AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
    
              $this->entityManager->flush();
              return $this->redirectToRoute('app_account_address');

        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/supprimer-une-addresse/{id}", name="app_account_delete_address")
     */
    public function delete($id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if($address || $address->getUser() == $this->getUser()){

            $this->entityManager->remove($address);
            $this->entityManager->flush();
        }
        
        

        return $this->redirectToRoute('app_account_address');
    }
}
