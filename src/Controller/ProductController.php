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

        $paginatorInt = 4;

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $productRepo->getProductPaginator($paginatorInt, $offset);

        return $this->render('product/display.html.twig', [
            'photourl' => $photoUrl,
            'photourlcar' => $photoUrlCar,
            'products' => $paginator,
            'previous' => $offset - $paginatorInt,
            'next' => min(count($paginator), $offset + $paginatorInt),
        ]);
    }

    #[Route('/productslist', name: 'productslist')]
    public function show(BrandRepository $brandRepo, ProductRepository $productRepo, Request $request, String $photoUrl, String $photoUrlCar): Response
    {
        // $brands = ['adidas', 'nike', 'puma', 'reebok'];
        // $brandsId = $brandRepo->getListBrandId();
        $paginatorInt = 9;

        $brands = $brandRepo->getListBrand();
        $brand_search = $request->query->get('brand_search', '');

        $names = $productRepo->getListName();
        $name_search = $request->query->get('name_search', '');

        $prices = $productRepo->getListPrice();
        $price_mini = $request->query->get('mini', '');
        $price_maxi = $request->query->get('maxi', '');

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $productRepo->getProductPaginator($paginatorInt, $offset, $name_search, $brand_search, $price_mini, $price_maxi);

        return $this->render('product/show.html.twig', [
            'brand_search' => $brand_search,
            'brands' => $brands,
            'price_maxi' => $price_maxi,
            'price_mini' => $price_mini,
            'name_search' => $name_search,
            'names' => $names,
            'photourl' => $photoUrl,
            'photourlcar' => $photoUrlCar,
            'products' => $paginator,
            'previous' => $offset - $paginatorInt,
            'next' => min(count($paginator), $offset + $paginatorInt),
        ]);
    }


    #[Route('/product/{id}', name: 'singleProduct')]
    public function singleProduct(Product $product, String $photoUrl, String $photoUrlCar): Response
    {
        return $this->render('product/singleProduct.html.twig', [
            'product' => $product,
            'photourl' => $photoUrl,
            'photourlcar' => $photoUrlCar,
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

            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/index.html.twig', [
            'form_product' => $form->createView()
        ]);
    }
}
