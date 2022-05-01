<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER")
     */
    public function success($id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService, EventDispatcher $dispatcher) {
        // Récupération de la commande
        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID )) {
                $this->addFlash("warning", "Cette commande n'existe pas !");
                return $this->redirectToRoute('purchase_index');
            }

        // La passer au statut PAYÉE
        $purchase->setStatus($purchase::STATUS_PAID);

        $em->flush();

        // Vider le panier
        $cartService->empty();

        // Lancer un évènement
        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $dispatcher->dispatch($purchaseEvent, 'purchase.success');

        // Rediriger avec un flash vers la liste des commandes
        $this->addFlash("success", "La commande a été payée et confirmée !");
        return $this->redirectToRoute("purchase_index");
    }
}