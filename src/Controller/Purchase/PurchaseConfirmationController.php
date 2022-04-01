<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PurchaseConfirmationController extends AbstractController
{
    protected $cartService;
    protected $persister;
    protected $em;

    public function __construct(CartService $cartService, PurchasePersister $persister,EntityManagerInterface $em)
    {
        $this->cartService = $cartService;
        $this->persister = $persister;
        $this->em = $em;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmer une commande")
     */
    public function confirm(Request $request)
    {
        // 1. Nous voulons lire les données du formulaire
        // FormFactoryInterface / Request
        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);

        // 2. Si le formulaire n'a pas été soumis : dégager
        if(!$form->isSubmitted()) {
            // Message Flash puis redirection
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
            return $this->redirectToRoute('cart_show');
        }
        
        // 4. S'il n'y a pas de produits dans mon panier : dégager
        $cartItems = $this->cartService->getDetailedCartItems();

        if(count($cartItems) === 0) {
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');

            return $this->redirectToRoute('cart_show');
        }

        // 5. Nous allons créer une Purchase
        /** @var Purchase */
        $purchase = $form->getData();

        $this->persister->storePurchase($purchase);

        return $this->redirectToRoute('purchase_payment_form', [
            'id' => $purchase->getId()
        ]);

    }
}
