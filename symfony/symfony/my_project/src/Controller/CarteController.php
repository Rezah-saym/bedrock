<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CarteController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/mon-panier", name="app_carte")
     */
    public function index(Cart $cart): Response
    {

        return $this->render('carte/index.html.twig', [
            'cart' => $cart->getFull()
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_carte")
     */
    public function add(Cart $cart, $id): Response
    {

        $cart->add($id);
        return $this->redirectToRoute('app_carte');

    }

    /**
     * @Route("/cart/remove", name="remove_my_carte")
     */
    public function remove(Cart $cart): Response
    {
        $cart->remove();
        return $this->redirectToRoute('app_products');

    }

    /**
     * @Route("/cart/delete/{id}", name="delete_to_carte")
     */
    public function delete(Cart $cart, $id): Response
    {
        $cart->delete($id);
        return $this->redirectToRoute('app_carte');

    }

    /**
     * @Route("/cart/decrease/{id}", name="decrease_to_carte")
     */
    public function decrease(Cart $cart, $id): Response
    {
        $cart->decrease($id);
        return $this->redirectToRoute('app_carte');

    }

    /**
     * @Route("/cart/addone/{id}", name="one_to_carte")
     */
    public function addone(Cart $cart, $id): Response
    {

        $cart->add($id);
        // Retrieve the slug of the added product using the EntityManager
        $product = $this->entityManager->getRepository(Product::class)->find($id);
        $slug = $product->getSlug();
        return $this->redirectToRoute('app_product', ['slug' => $slug]);

    }

    /**
     * @Route("/cart/removeone/{id}", name="remove_one_carte")
     */
    public function removeone(Cart $cart, $id): Response
    {

        $cart->decrease($id);
        // Retrieve the slug of the added product using the EntityManager
        $product = $this->entityManager->getRepository(Product::class)->find($id);
        $slug = $product->getSlug();
        return $this->redirectToRoute('app_product', ['slug' => $slug]);

    }


}
