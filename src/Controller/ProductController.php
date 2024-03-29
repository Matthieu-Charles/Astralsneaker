<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\BrandRepository;
use App\Repository\ProductRepository;
use App\Repository\SizeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    #[Route('/', name: 'homepage')]
    public function index(SizeRepository $sizeRepo, ProductRepository $productRepo, Request $request, String $photoUrl, String $photoUrlCar): Response
    {

        $paginatorInt = 4;

        $sizes = $sizeRepo->getListSize();

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $productRepo->getProductPaginator($paginatorInt, $offset);

        return $this->render('product/display.html.twig', [
            'sizes' => $sizes,
            'photourl' => $photoUrl,
            'photourlcar' => $photoUrlCar,
            'products' => $paginator,
            'previous' => $offset - $paginatorInt,
            'next' => min(count($paginator), $offset + $paginatorInt),
        ]);
    }

    #[Route('/productslist', name: 'productslist')]
    public function show(SizeRepository $sizeRepo, BrandRepository $brandRepo, ProductRepository $productRepo, Request $request, String $photoUrl, String $photoUrlCar): Response
    {
        $paginatorInt = 9;

        $sizes = $sizeRepo->getListSize();

        $brands = $brandRepo->getListBrand();
        $brand_search = $request->query->all('brand_search', '');

        $price_mini = $request->query->get('mini', '');
        $price_maxi = $request->query->get('maxi', '');

        $name_search = $request->query->get('name_search', '');

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $productRepo->getProductPaginator($paginatorInt, $offset, $name_search, $brand_search, $price_mini, $price_maxi);

        return $this->render('product/show.html.twig', [
            'sizes' => $sizes,
            'name_search' => $name_search,
            'brand_search' => $brand_search,
            'brands' => $brands,
            'price_maxi' => $price_maxi,
            'price_mini' => $price_mini,
            'photourl' => $photoUrl,
            'photourlcar' => $photoUrlCar,
            'products' => $paginator,
            'previous' => $offset - $paginatorInt,
            'next' => min(count($paginator), $offset + $paginatorInt),
        ]);
    }


    #[Route('/product/{id}', name: 'singleProduct')]
    public function singleProduct(Request $request, SizeRepository $sizeRepo, Product $product, String $photoUrl, String $photoUrlCar): Response
    {
        $sizes = $sizeRepo->getListSize();

        $size_search = $request->query->get('size_search', '');

        return $this->render('product/singleProduct.html.twig', [
            'size_search' => $size_search,
            'sizes' => $sizes,
            'product' => $product,
            'photourl' => $photoUrl,
            'photourlcar' => $photoUrlCar,
        ]);
    }

    #[Route('/admin/product/{id}/delete', name: 'delProduct')]
    public function delProduct(Product $product, ProductRepository $productRepo): Response {

        $productRepo->remove($product, true);
        return $this->redirectToRoute('productslist');
        
    }

    #[Route('/admin/product/add', name: 'product_add')]
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

            return $this->redirectToRoute('product_add');
        }

        return $this->render('product/index.html.twig', [
            'form_product' => $form->createView()
        ]);
    }

    #[Route('/admin/product/update/{id}', name: 'product_update')]
    public function updateProduct(Product $product ,ProductRepository $productRepo, Request $request, string $photoDir): Response
    {
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

            return $this->redirectToRoute('productslist');
        }

        return $this->render('product/index.html.twig', [
            'form_product' => $form->createView()
        ]);
    }
}
