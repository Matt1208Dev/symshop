<?php

namespace App\Controller\Purchase;

use Knp\Snappy\Pdf;
use App\Entity\User;
use App\Repository\PurchaseRepository;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Twig\Environment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchasesListController extends AbstractController
{
    /**
     * @Route("/purchases", name="purchase_index")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour accéder à vos commandes")
     */
    public function index()
    {
        // 1. Nous assurer que la personne est connectée (sinon redirection vers la page d'accueil)

        /**
         * @var User
         */
        $user = $this->getUser();

        // if (!$user) {
            
        //     throw new AccessDeniedException("Vous devez être connecté pour accéder à vos commandes");
        // }

        // 2. Nous voulons savoir qui est connecté.

        // 3. Nous voulons passer l'utilisateur connecté à Twig afin d'afficher ses commandes.
        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
    }

    /**
     * @Route("/purchase/{id}/pdf", name="purchase_pdf")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour accéder à vos commandes")
     */
    public function pdfAction($id, PurchaseRepository $purchaseRepository,Pdf $pdf)
    {
        /**
         * @var User
         */
        $user = $this->getUser();

        $purchase = $purchaseRepository->find($id);

        $html = $this->renderView('purchase/purchase_pdf.html.twig', [
            'purchase' => $purchase,
            'user' => $user
        ]);

        return new PdfResponse(
            $pdf->getOutputFromHtml($html, [
                'encoding' => 'utf-8'
            ]),
            $id . '.pdf'
        );
    }
}
