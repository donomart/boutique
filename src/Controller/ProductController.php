<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\SearchFilters;
use App\Form\CommentType;
use App\Form\SearchFiltersType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/nos-produits", name="products")
     */
    public function index(ProductRepository $repo, Request $request): Response
    {
        //$repo = $this->getDoctrine()->getRepository(Product::class);
        //$products = $repo->findBy(['category' => '147'], ['price' => 'asc'], 1, 0);

        $searchFilters = new SearchFilters();

        $form = $this->createForm(SearchFiltersType::class, $searchFilters);
        $form->handleRequest($request);

        $categories = $searchFilters->getCategories();
        $error = null;


        if ($form->isSubmitted() && $form->isValid()) {
            /*foreach ($categories as $category) {
                $idCat[] = $category->getId();
            }*/

            //$products = $repo->findBy(['category' => $idCat]);
            $products = $repo->myFindSearch($searchFilters);
        } else
            $products = $repo->findAll();


        //
        //$repo->myFindProductPrice(5, 100);
        //$repo->myFindSearch($searchFilters);
        //


        if (count($products) < 1)
            $error = "Aucun produit ne correspond à votre recherche.";
        else
            $error = null;


        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
            'error' => $error
        ]);
    }


    /**
     * @Route("produit/{slug}", name="show_product")
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }


    /**
     * @Route("compte/mes-commandes/{slug}/commentaire", name="products_comment")
     */
    public function comment(Product $product, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setUser($this->getUser());
            $comment->setProduct($product);

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', 'Le commentaire à été ajouté.');

            return $this->redirectToRoute('show_product', ['slug' => $product->getSlug()]);
        }


        return $this->render('product/comment.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }
}
