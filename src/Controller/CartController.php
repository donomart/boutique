<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Services\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/mon-panier", name="cart")
     */
    public function index(Cart $cart, ProductRepository $repo): Response
    {
        $productsInCart = [];

        foreach ($cart->get() as $id => $quantity) {
            $product = [
                'product' => $repo->findOneById($id),
                'quantity' => $quantity
            ];

            $productsInCart[] = $product;
        }


        return $this->render('cart/index.html.twig', [
            'cart' => $productsInCart
        ]);
    }


    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     */
    public function add($id, Cart $cart): Response
    {
        $cart->add($id);

        return $this->redirectToRoute('cart');
    }


    /**
     * @Route("/cart/subtract/{id}", name="subtract_from_cart")
     */
    public function subtract($id, Cart $cart): Response
    {
        $cart->substract($id);

        return $this->redirectToRoute('cart');
    }


    /**
     * @Route("/cart/remove/{id}", name="remove_from_cart")
     */
    public function remove($id, Cart $cart): Response
    {
        $cart->remove($id);

        return $this->redirectToRoute('cart');
    }


    /**
     * @Route("/cart/empty", name="empty_cart")
     */
    public function empty(Cart $cart): Response
    {
        $cart->empty();

        return $this->redirectToRoute('cart');
    }
}
