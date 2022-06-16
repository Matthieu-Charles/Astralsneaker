<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Product;
use App\Repository\BrandRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Description'
            ])
            ->add('brand', EntityType::class, [
                'label' => 'Marque',
                'class' => Brand::class,
                'query_builder' => function (BrandRepository $brand) {
                    return $brand->createQueryBuilder('b')
                        ->orderBy('b.name');
                },
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'attr' => ['class' => 'col-3']
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock'
            ])
            ->add('photo', FileType::class, [
                'required' => false,
                'mapped' => false,
            ])
            ->add('Enregistrer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
