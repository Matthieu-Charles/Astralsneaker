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

    #[Route('/cart/success', name: 'cart_success')]
    public function success(): Response
    {
        return $this->render('success.html.twig', [
            'link' => 'homepage',
            'text' => 'Votre commande a bien été enregistré',
            'textLink' => 'Cliquez ici afin de retourner sur la page d\'accueil',
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

        $order = new Order;
        $user = $this->getUser();

        $date = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $order->setUser($user)->setTotal($cartService->getTotal())->setCreatedAt($date);
        $orderRepository->add($order, true);

        $cartWithData = $cartService->getFullCart();

        foreach ($cartWithData as $data) {

            $orderItem = new OrderItem;
            $orderItem->setOrderRef($order)->setProduct($data['product'])->setPrice($data['product']->getPrice())->setQuantity($data['quantity']);
            $orderItemRepository->add($orderItem, true);

           
            $product = $productRepository->find($data['product']->getId());
            $product->setStock($product->getStock() - $data['quantity']);
            $productRepository->add($product, true);
        }

        $session->getSession()->remove('cart');

        return $this->redirectToRoute('cart_success');
    }
}
