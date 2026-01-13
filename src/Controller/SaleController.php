<?php

namespace App\Controller;

use App\Entity\Sale;
use App\Form\SaleType;
use App\Repository\SaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sale')]
final class SaleController extends AbstractController
{
    #[Route(name: 'app_sale_index', methods: ['GET'])]
    public function index(SaleRepository $saleRepository): Response
    {
        return $this->render('sale/index.html.twig', [
            'sales' => $saleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sale_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sale = new Sale();
        // On met la date d'aujourd'hui par défaut
        $sale->setDate(new \DateTime()); 
        
        $form = $this->createForm(SaleType::class, $sale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 1. Récupérer le produit lié à la vente
            $product = $sale->getProduct();
            $quantitySold = $sale->getQuantity();

            // 2. Vérifier s'il y a assez de stock
            if ($product->getQuantity() < $quantitySold) {
                $this->addFlash('danger', 'Stock insuffisant ! Il ne reste que ' . $product->getQuantity() . ' articles.');
                return $this->redirectToRoute('app_sale_new');
            }

            // 3. Mettre à jour le stock (Logique Métier 20/20)
            $product->setQuantity($product->getQuantity() - $quantitySold);

            // 4. Sauvegarder tout
            $entityManager->persist($sale);
            $entityManager->persist($product); // Important : on sauvegarde aussi le produit modifié
            $entityManager->flush();

            $this->addFlash('success', 'Vente enregistrée et stock mis à jour !');

            return $this->redirectToRoute('app_sale_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sale/new.html.twig', [
            'sale' => $sale,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sale_show', methods: ['GET'])]
    public function show(Sale $sale): Response
    {
        return $this->render('sale/show.html.twig', [
            'sale' => $sale,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sale_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sale $sale, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SaleType::class, $sale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sale_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sale/edit.html.twig', [
            'sale' => $sale,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sale_delete', methods: ['POST'])]
    public function delete(Request $request, Sale $sale, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sale->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sale);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sale_index', [], Response::HTTP_SEE_OTHER);
    }
}
