<?php

namespace App\Controller;

use DateTime;
use App\Classe\Cart;
use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/commande", name="app_order")
     */
    public function index(Cart $cart, Request $request): Response
    {
        if(!$this->getUser()->getAddresses()->getValues())
        {
            return $this->redirectToRoute('app_account_add_address');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user'  => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
            
    }

    /**
     * @Route("/commande/recapitulatif", name="app_order_recap", methods={"POST"})
     */
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user'  => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
           
            $date = new DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content  = $delivery->getFirstname().' '.$delivery->getLastname();

            if($delivery->getCompany()){
                $delivery_content .=  '<br/>'.$delivery->getCompany();
            }
            $delivery_content .='<br/>'.$delivery->getAddress();
            $delivery_content .='<br/>'.$delivery->getPostal().' '. $delivery->getCity();
            $delivery_content .='<br/>'.$delivery->getCountry();

            
            //save my order, Order
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);
            
            $this->entityManager->persist($order);
            
            foreach($cart->getFull() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($orderDetails);
                
            }

            //$this->entityManager->flush();
           //save my product, OrderDetails()
           return $this->render('order/add.html.twig', [
            'cart' => $cart->getFull(),
            'carrier' => $carriers,
            'delivery' => $delivery_content
        ]);
        }

        return $this->redirectToRoute('app_carte');


            
    }
}
