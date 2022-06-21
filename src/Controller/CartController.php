<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }
    
    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add($id, Request $request): Response
    {
        $session = $request->getSession();

        $panier = $session->get('panier', []);

        $panier[$id] = 1;

        $session->set('panier', $panier);

        dd($session->get('panier'));
    }
}
