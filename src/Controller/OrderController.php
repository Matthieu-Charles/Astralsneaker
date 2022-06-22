<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'order')]
    public function show(OrderRepository $orderRepo, Request $request): Response
    {
        $paginatorInt = 12;

        $order_search = $request->query->get('order_search', '');     

        $total_mini_search = $request->query->get('total_mini_search', '');
        $total_maxi_search = $request->query->get('total_maxi_search', '');
        
        $date_mini_search = $request->query->get('date_mini_search', '');
        $date_maxi_search = $request->query->get('date_maxi_search', '');

        $number_search = $request->query->get('number_search', '');

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $orderRepo->getOrderPaginator($paginatorInt, $offset, $order_search, $total_mini_search, $total_maxi_search, $date_mini_search, $date_maxi_search);

        return $this->render('order/index.html.twig', [
            'order_search' => $order_search,
            'total_mini_search' => $total_mini_search,
            'total_maxi_search' => $total_maxi_search,
            'date_mini_search' => $date_mini_search,
            'date_maxi_search' => $date_maxi_search,
            'number_search' => $number_search,
            'orders' => $paginator,
            'previous' => $offset - $paginatorInt,
            'next' => min(count($paginator), $offset + $paginatorInt),
        ]);
    }

    #[Route('/order/{id}', name: 'singleorder')]
    public function singleOrder(OrderRepository $orderRepo, OrderItemRepository $orderItemRepo, Request $request): Response
    {
        $paginatorInt = 12;
      
        $order_items = $orderItemRepo;

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $orderRepo->getOrderPaginator($paginatorInt, $offset);

        return $this->render('order/singleOrder.html.twig', [
            'order_items' => $order_items,
            'orders' => $paginator,
            'previous' => $offset - $paginatorInt,
            'next' => min(count($paginator), $offset + $paginatorInt),
        ]);
    }

    #[Route('/order/{id}/delete', name: 'delOrder')]
    public function delOrder(Order $order, OrderRepository $orderRepo): Response {

        $orderRepo->remove($order, true);
        return $this->redirectToRoute('order');
        
    }
}