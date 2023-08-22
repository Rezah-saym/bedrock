<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Classe\Cart;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }



    /**
     * @Route("/nos-produits", name="app_products")
     */
    public function index(Request $request): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll(); 
        
        $search = new Search();
        $form= $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search); 
        }

        return $this->render('product/index.html.twig', [
              'products' => $products,
              'form'     =>$form->createView()
           
        ]);
    }

    /**
     * @Route("/produit/{slug}", name="app_product")
     */
    public function show($slug, Cart $cart): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug); 

        if (!$products){
            return $this->redirectToRoute('app_products');
        }
        
        $id_product = $products->getId();
        $product_complete = $cart->getShowProduct($id_product);

        return $this->render('product/show.html.twig', [
            'products' => $product_complete,
            
         
      ]);

   }
}
