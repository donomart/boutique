<?php

namespace App\Controller;

use App\Services\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{
    /**
     * @Route("/compte/adresses", name="account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig', []);
    }


    /**
     * @Route("/compte/ajouter-une-adresse", name="account_address_add")
     */
    public function add(Request $request, EntityManagerInterface $manager, Cart $cart): Response
    {
        $address = new Address();

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $address->setUser($user);

            $manager->persist($address);
            $manager->flush();

            $this->addFlash('success', 'L\'adresse a été ajoutée!');

            if ($cart->get())
                return $this->redirectToRoute('order');
            else
                return $this->redirectToRoute('account_addres');


            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_add.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/compte/modifier-une-adresse/{id}", name="account_address_edit")
     */
    public function edit(Address $address, Request $request, EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser();
        $addressOwner = $address->getUser();

        if ($currentUser != $addressOwner) {
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash('success', 'L\'adresse a été modifiée avec succès!');
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_add.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/compte/supprimer-une-adresse/{id}", name="account_address_delete")
     */
    public function delete(Address $address, Request $request, EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser();
        $addressOwner = $address->getUser();

        if ($currentUser != $addressOwner) {
            return $this->redirectToRoute('account_address');
        }

        $manager->remove($address);
        $manager->flush();

        $this->addFlash('success', 'L\'adresse a été supprimée avec succès!');

        return $this->redirectToRoute('account_address');
    }
}
