<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    #[Route('/', name: 'homepage')]
    public function index(ProductRepository $productRepo, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $productRepo->getProductPaginator($offset);


        return $this->render('product/display.html.twig', [
            'products' => $paginator,
            'previous' => $offset - ProductRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + ProductRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/product', name: 'app_product')]
    public function addProduct(ProductRepository $productRepo, Request $request, string $photoDir): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductFormType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $product->setPhotoFileName($filename);
            }

            $productRepo->add($product, true);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('product/index.html.twig', [
            'form_product' => $form->createView()
        ]);
    }
}
