<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products_item_list', methods: ['GET'])]
    public function list(): Response|JsonResponse
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

    #[Route('/products', name: 'app_products_item_new', methods: ['POST'])]
    public function create(): Response|JsonResponse
    {

    }

    #[Route('/products/{id}', name: 'app_products_item', methods: ['GET'])]
    public function getProduct(int $id): Response|JsonResponse
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

        foreach ($fakeData as $data) {
            if ($data['id'] !== $id) {
                continue;
            }

            return $this->json($data);
        }

        return new Response(status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/products/{id}', name: 'app_products_item_update', methods: ['PUT'])]
    public function updateProduct(int $id): Response|JsonResponse
    {

    }
}
