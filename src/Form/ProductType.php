<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\DataTransformer\CentimesTransformer;
use App\Form\Type\PriceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom du produit",
                'attr' => [
                    'placeholder' => "Tapez le nom du produit"
                ]
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => "Description",
                'attr' => [

                    'placeholder' => "Tapez une description courte mais parlante pour l'utilisateur"
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => "Prix",
                'attr' => [

                    'placeholder' => "Tapez le prix du produit en €"
                ],
                'divisor' => 100
            ])
            ->add('category', EntityType::class, [
                'label' => "Catégorie",
                'placeholder' => "-- Choisir une catégorie --",
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('mainPicture', UrlType::class, [
                'label' => 'Image du produit',
                'attr' => [
                    'placeholder' => "Tapez une URL d'image"
                ]
            ]);

            // Utilisation de la classe CentimesTransformer
            // $builder->get('price')->addModelTransformer(new CentimesTransformer);

            // Utilisation de la classe CallbackTransformer de Symfony
            // $builder->get('price')->addModelTransformer(new CallbackTransformer(
            //     function($value) {
            //         if($value === null) {
            //             return;
            //         }
            //         return $value / 100;
            //     },
            //     function ($value) {
            //         if($value === null) {
            //             return;
            //         }

            //         return $value * 100;
            //     }
            // ));

        // Ajout d'un écouteur d'évènement
        // $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
        //     $product = $event->getData();

        //     if($product->getPrice() !== null) {
        //         $product->setPrice($product->getPrice() * 100);
        //     }
        // });

        // $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
        //     $form = $event->getForm();

        //     /**
        //      * @var Product
        //      */
        //     $product = $event->getData();

        //     if ($product->getId() === null) {
        //         $form->add('category', EntityType::class, [
        //             'label' => "Catégorie",
        //             'placeholder' => "-- Choisir une catégorie --",
        //             'class' => Category::class,
        //             'choice_label' => 'name'
        //         ]);
        //     }
        // });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
