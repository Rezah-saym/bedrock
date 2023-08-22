<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    private $requestStack;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function add($id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        // // Check if the item is already in the cart
        // $found = false;
        // foreach ($cart as &$item) {
        //     if ($item['id'] === $id) {
        //         $item['quantity']++;
        //         $found = true;
        //         break;
        //     }
        // }

        // // If item not found, add it to the cart
        // if (!$found) {
        //     $cart[] = [
        //         'id' => $id,
        //         'quantity' => 1,
        //     ];
        // }

        if(!empty($cart[$id])) {
            $cart[$id] ++;
        }else{
            $cart[$id] =1;
        }

        $session->set('cart', $cart);
    }

    public function get()
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart', []);
    }

    public function remove()
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart', []);
    }

    public function delete($id)
    {
        $session = $this->requestStack->getSession();
    
        $cart = $session->get('cart', []);
    
        if (array_key_exists($id, $cart)) {
            unset($cart[$id]);
            $session->set('cart', $cart);
        }
    
        return $cart; // Return the modified cart array, if needed
    }

    public function decrease($id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if($cart[$id] > 1) {
            $cart[$id]--;
        }else{
            unset($cart[$id]);
        }

        $session->set('cart', $cart); 

        return $cart;
    }

    public function getFull(){

        $cartComplete = [];

        if($this->get()){
            foreach($this->get() as $id => $quantity){
                $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);
                if(!$product_object){
                    $this->delete($id);
                    continue;
                }
                    $cartComplete[] = [
                        'product'   =>  $product_object, 
                        'quantity'  => $quantity
                    ];
                
            }
        }

        return $cartComplete;

    }

    public function getShowProduct($id_product){

            $product_complete = [];
            $quantityOut = 0;
            $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id_product);

            if($this->get()){
                        if(array_key_exists($id_product , $this->get())){

                                foreach($this->get() as $id => $quantityIn){

                                        if($id == $id_product){
                                                if(!$product_object){
                                                    $this->delete($id_product);
                                                    continue;
                                                }
                                                $product_complete[] = [
                                                    'product'   =>  $product_object, 
                                                    'quantity'  => $quantityIn
                                                ];
                                        }
                                }
                        }else{
                                    $product_complete[] = [
                                        'product'   =>  $product_object, 
                                        'quantity'  => $quantityOut
                                    ];
                        }
            }else{
                        $product_complete[] = [
                            'product'   =>  $product_object, 
                            'quantity'  => $quantityOut
                        ];
            }

            return $product_complete;

    }

}
