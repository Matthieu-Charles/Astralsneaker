<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\BrandRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    #[Route('/', name: 'homepage')]
    public function index(ProductRepository $productRepo, Request $request, String $photoUrl, String $photoUrlCar): Response
    {
        $paginatorPerPage = 4;
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $productRepo->getProductPaginator($paginatorPerPage, $offset);

        return $this->render('product/display.html.twig', [
            'photourl' => $photoUrl,
            'photourlcar' => $photoUrlCar,
            'products' => $paginator,
            'previous' => $offset - ProductRepository::PAGINATOR_PER_PAGE_1,
            'next' => min(count($paginator), $offset + ProductRepository::PAGINATOR_PER_PAGE_1),
        ]);
    }

    #[Route('/productslist', name: 'productslist')]
    public function show(BrandRepository $brandRepo, ProductRepository $productRepo, Request $request, String $photoUrl, String $photoUrlCar): Response
    {
        // $brands = ['adidas', 'nike', 'puma', 'reebok'];
        // $brandsId = $brandRepo->getListBrandId();
        $paginatorPerPage = 9;

        $brands = $brandRepo->getListBrand();
        $brand_search = $request->query->get('brand_search', '');

        $names = $productRepo->getListName();
        $name_search = $request->query->get('name_search', '');

        $prices = $productRepo->getListPrice();
        $price_mini = $request->query->get('mini', '');
        $price_maxi = $request->query->get('maxi', '');

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $productRepo->getProductPaginator($paginatorPerPage, $offset, $name_search, $brand_search, $price_mini, $price_maxi);

        return $this->render('product/show.html.twig', [
            'brand_search' => $brand_search,
            'brands' => $brands,
            // 'brandsId' => $brandsId,
            'price_maxi' => $price_maxi,
            'price_mini' => $price_mini,
            'name_search' => $name_search,
            'names' => $names,
            'photourl' => $photoUrl,
            'photourlcar' => $photoUrlCar,
            'products' => $paginator,
            'previous' => $offset - ProductRepository::PAGINATOR_PER_PAGE_2,
            'next' => min(count($paginator), $offset + ProductRepository::PAGINATOR_PER_PAGE_2),
        ]);

        //////
        // Fonction qui marche 

        // $offset = max(0, $request->query->getInt('offset', 0));
        // $paginator = $productRepo->getProductPaginator($offset);


        // return $this->render('product/show.html.twig', [
        //     'photourl' => $photoUrl,
        //     'photourlcar' => $photoUrlCar,
        //     'products' => $paginator,
        //     'previous' => $offset - ProductRepository::PAGINATOR_PER_PAGE,
        //     'next' => min(count($paginator), $offset + ProductRepository::PAGINATOR_PER_PAGE),
        // ]);
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
