<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_product', methods: ['GET', 'POST'])]
    public function index(): JsonResponse
    {
        $fakeData = [
            [
                'id' => 1,
                'name' => 'Adidas Schuhe',
                'price' => 199.99,
            ],
            [
                'id' => 2,
                'name' => 'Nike Schuhe',
                'price' => 299.99,
            ],
            [
                'id' => 3,
                'name' => 'Puma Schuhe',
                'price' => 99.99,
            ],
        ];

        return $this->json($fakeData);
    }
}
