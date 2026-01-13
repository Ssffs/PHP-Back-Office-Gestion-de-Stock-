<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SaleRepository;
use App\Repository\UserRepository; // Si tu veux compter les users
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        ProductRepository $productRepo,
        CategoryRepository $categoryRepo,
        SaleRepository $saleRepo,
        UserRepository $userRepo
    ): Response
    {
        // 1. Calcul de la valeur totale du stock (Logique Métier +++)
        $products = $productRepo->findAll();
        $totalStockValue = 0;
        foreach ($products as $product) {
            // Prix * Quantité en stock
            $totalStockValue += $product->getPrice() * $product->getQuantity();
        }

        // 2. Récupérer les 5 dernières ventes pour l'affichage "Activités récentes"
        // (Suppose que tu as un champ 'date' ou 'id' pour trier)
        $latestSales = $saleRepo->findBy([], ['id' => 'DESC'], 5);

        return $this->render('dashboard/index.html.twig', [
            'countProducts' => $productRepo->count([]),
            'countCategories' => $categoryRepo->count([]),
            'countSales' => $saleRepo->count([]),
            'countUsers' => $userRepo->count([]),
            'stockValue' => $totalStockValue,
            'latestSales' => $latestSales
        ]);
    }
}