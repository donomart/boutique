<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Order;
use App\Services\Cart;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * @Route("/commande", name="order")
     */
    public function index(Request $request, ProductRepository $repo, EntityManagerInterface $manager, Cart $cart): Response
    {
        if (count($this->getUser()->getAddresses()) == 0) {
            return $this->redirectToRoute('account_address_add');
        }


        $productsInCart = [];

        foreach ($cart->get() as $id => $quantity) {
            $product = [
                'product' => $repo->findOneById($id),
                'quantity' => $quantity
            ];

            $productsInCart[] = $product;
        }


        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime();
            $date = $date->format('dmY');

            $order = new Order();
            $order->setUser($this->getUser())
                ->setCreatedAt(new \DateTime())
                ->setCarrier($form->get('carriers')->getData())
                ->setDelivery($form->get('addresses')->getData())
                ->setStatus(0)
                ->setReference($date . '-' . uniqid());

            $manager->persist($order);


            foreach ($productsInCart as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order)
                    ->setProduct($product['product'])
                    ->setPrice($product['product']->getPrice())
                    ->setQuantity($product['quantity']);

                $productsForStripe[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $product['product']->getName(),
                            'images' => [$_SERVER['HTTP_ORIGIN'] . '/uploads/' . $product['product']->getIllustration()]
                        ],
                        'unit_amount' => $product['product']->getPrice()
                    ],
                    'quantity' => $product['quantity']
                ];

                $manager->persist($orderDetails);
            }

            //$manager->flush();

            $productsForStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $order->getCarrier()->getName()
                    ],
                    'unit_amount' => $order->getCarrier()->getPrice()
                ],
                'quantity' => 1
            ];


            // Stripe checkout
            Stripe::setApiKey($this->getParameter('stripe_key'));
            
            $checkout_session = \Stripe\Checkout\Session::create([
                /*'line_items' => [[
                    'price' => 'price_1KW0CyBjSjpFqgvnjYkSssbu',
                    'quantity' => 1,
                ]],*/
                'customer_email' => $this->getUser()->getEmail(),
                'line_items' => $productsForStripe,
                'mode' => 'payment',
                'success_url' => $_SERVER['HTTP_ORIGIN'] . '/commande/merci/{CHECKOUT_SESSION_ID}',
                'cancel_url' => $_SERVER['HTTP_ORIGIN'] . '/commande/erreur/{CHECKOUT_SESSION_ID}',
            ]);


            $order->setCheckoutSessionId($checkout_session->id);
            $manager->persist($order);
            $manager->flush();


            return $this->render('order/recap.html.twig', [
                'cart' => $productsInCart,
                'order' => $order,
                'stripeUrl' => $checkout_session->url
            ]);
        }


        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $productsInCart
        ]);
    }
}
