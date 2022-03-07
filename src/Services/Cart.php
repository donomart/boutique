<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Session\SessionInterface;


class Cart
{
    private $session;


    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }


    public function add($id)
    {
        $cart = $this->session->get('cart', []);

        if (!empty($cart[$id]))
            $cart[$id]++;
        else
            $cart[$id] = 1;

        return $this->session->set('cart', $cart);
    }


    public function substract($id)
    {
        $cart = $this->session->get('cart', []);

        if (!empty($cart[$id])) {
            if ($cart[$id] === 1)
                unset($cart[$id]);
            else
                $cart[$id]--;
        }
        
        return $this->session->set('cart', $cart);
    }


    public function remove($id)
    {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]);

        return $this->session->set('cart', $cart);
    }


    public function empty()
    {
        return $this->session->remove('cart');
    }


    public function get()
    {
        return $this->session->get('cart', []);
    }
}
