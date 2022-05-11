<?php

namespace App\Controller;

use App\Entity\Product;
use App\Event\ProductViewEvent;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category", priority=-1)
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show", priority=-1)
     */
    public function show($slug, ProductRepository $productRepository, EventDispatcherInterface $dispatcher)
    {
        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas");
        }

        // Lancer un évènement
        $productViewEvent = new ProductViewEvent($product);
        $dispatcher->dispatch($productViewEvent, 'product.view');

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        // Validation via consultation par le Validator d'un fichier YAML (product.yaml)
        $product = new Product;

        $resultat = $validator->validate($product);

        if ($resultat->count() > 0) {

            dd("Il y a des erreurs", $resultat);
        }

        dd("Tout va bien", $resultat);

        // Validation de données simples (scalaires)
        // $age = 200;

        // $resultat = $validator->validate($age, [
        //     new LessThanOrEqual([
        //         'value' => 120,
        //         'message' => "L'âge doit être inférieur à {{ compared_value }} mais vous avez donné {{ value }}"
        //     ]),
        //     new GreaterThan([
        //         'value' => 0,
        //         'message' => "L'âge doit être supérieur à 0"
        //     ])
        // ]);

        // Validation de données complexes (tableaux)
        // $client = [
        //     'nom' => 'Gueulle',
        //     'prenom' => '',
        //     'voiture' => [
        //         'marque' => 'Renault',
        //         'couleur' => 'Noire'
        //     ]
        // ];

        // $collection = new Collection([
        //     'nom' => new NotBlank(['message' => "Le nom ne doit pas être vide"]),
        //     'prenom' => [
        //         new NotBlank(['message' => "Le prénom ne doit pas être vide"]),
        //         new Length(['min' => 3, 'minMessage' => "Le prénom ne doit pas faire moins de 3 caractères"])
        //     ],
        //     'voiture' => new Collection([
        //         'marque' => new NotBlank(['message' => "La marque de la voiture est obligatoire"]),
        //         'couleur' => new NotBlank(['message' => "La couleur est obligatoire"])
        //     ])
        // ]);

        // $resultat = $validator->validate($client, $collection);

        // if ($resultat->count() > 0) {

        //     dd("Il y a des erreurs", $resultat);
        // }

        // dd("Tout va bien", $resultat);

        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);

        // $form->setData($product);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $product = $form->getData();
            $product->setSlug(strtolower($slugger->slug($product->getName())));

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute("product_show", [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
