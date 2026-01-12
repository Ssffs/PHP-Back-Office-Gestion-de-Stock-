<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(ProductRepository $productRepository): Response
    {
        // On compte les produits
        $totalProducts = $productRepository->count([]);

        return $this->render('dashboard/index.html.twig', [
            'totalProducts' => $totalProducts,
        ]);
    }
}
