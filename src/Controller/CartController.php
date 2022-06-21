<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_index')]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal()
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add($id, CartService $cartService): Response
    {
        $cartService->add($id);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove($id, CartService $cartService)
    {
        $cartService->remove($id);

        return $this->redirectToRoute("cart_index");
    }

    #[Route('/cart/validate', name: 'cart_validate')]
    public function validate(ProductRepository $productRepository, OrderRepository $orderRepository, CartService $cartService, OrderItemRepository $orderItemRepository, RequestStack $session)
    {

        //on instancie une nouvelle commande
        $order = new Order;
        //on récupère le client courant, $this->getUser() nous renvoie l'admin courant auquel on fait un getClient()
        $user = $this->getUser();

        // on récupère la date du jour au bon format
        $date = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        //on définit les valeurs de la commande
        $order->setUser($user)->setTotal($cartService->getTotal())->setCreatedAt($date);
        //on ajoute la commande en base
        $orderRepository->add($order, true);

        //on récupère le contenu complet du panier dans une variable (voir le service panier)
        $cartWithData = $cartService->getFullCart();

        //pour chaque data de produit du panier
        foreach ($cartWithData as $data) {

            //on instancie un nouveau contenu
            $orderItem = new OrderItem;
            // on lui attribue les valeurs correspondantes à la data en cours
            $orderItem->setOrderRef($order)->setProduct($data['product'])->setPrice($data['product']->getPrice())->setQuantity($data['quantity']);
            // on ajoute la data en base
            $orderItemRepository->add($orderItem, true);

            //on récupère le produit courante avec l'id
            $product = $productRepository->find($data['product']->getId());
            //on met à jour le stock
            $product->setStock($product->getStock() - $data['quantity']);
            // on met à jour en bdd
            $productRepository->add($product, true);
        }
        // on reinitialise le panier à un tableau vide
        $session->getSession()->set('cart', []);

        return $this->redirectToRoute('productslist');
    }
}
