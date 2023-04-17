<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    public function __construct(private readonly ProductRepository $repository)
    {
    }

    #[Route('/products', name: 'app_products_item_list', methods: ['GET'])]
    public function list(): Response|JsonResponse
    {
        return $this->json($this->repository->findAll());
    }

    #[Route('/products', name: 'app_products_item_new', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer): Response|JsonResponse
    {
        try {
            $content = $request->getContent();
            $product = $serializer->deserialize($content, Product::class, 'json');
            $this->repository->save($product, true);
        } catch (UniqueConstraintViolationException $exception) {
            return new Response('A product with the same name already exists.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json($product);
    }

    #[Route('/products/{id}', name: 'app_products_item', methods: ['GET'])]
    public function getProduct(int $id): Response|JsonResponse
    {
        $product = $this->repository->find($id);
        if (null === $product) {
            return new Response(null, status: Response::HTTP_NOT_FOUND);
        }

        return $this->json($product);
    }

    #[Route('/products/{id}', name: 'app_products_item_update', methods: ['PUT'])]
    public function updateProduct(
        Request $request,
        SerializerInterface $serializer,
        Product $product
    ): Response|JsonResponse {
        $serializer->deserialize(
            $request->getContent(),
            Product::class,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $product
            ]
        );
        $this->repository->save($product, true);

        return $this->json($product);
    }
}
