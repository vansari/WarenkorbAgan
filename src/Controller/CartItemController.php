<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/items')]
class CartItemController extends AbstractController
{
    public function __construct(
        private readonly CartItemRepository $repository,
        private readonly ProductRepository $productRepository
    ) {

    }

    #[Route('', name: 'app_cart_item_list', methods: ['GET'])]
    public function getList(): JsonResponse
    {
        return $this->json($this->repository->findAll());
    }

    #[Route('/{id}', name: 'app_cart_item', methods: ['GET'])]
    public function getItem(?CartItem $item): Response|JsonResponse
    {
        if (null === $item) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        return $this->json($item);
    }

    #[Route('/{id}/products', name: 'app_cart_item_product', methods: ['GET'])]
    public function getProductOfItem(?CartItem $cartItem): Response|JsonResponse
    {
        if (null === $cartItem) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        return $this->json($cartItem->getProduct());
    }

    #[Route('', name: 'app_cart_item_create', methods: ['POST'])]
    public function createItem(Request $request): Response|JsonResponse
    {
        $content = json_decode($request->getContent());
        if (!property_exists($content, 'quantity') && !property_exists($content, 'product')) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        if (null === ($product = $this->productRepository->find($content->product))) {
            return new Response('unknown product', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $cartItem = new CartItem();
        $cartItem
            ->setProduct($product)
            ->setQuantity($content->quantity);

        $this->repository->save($cartItem, true);

        return $this->json($cartItem, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_cart_item_delete', methods: ['DELETE'])]
    public function deleteItem(?CartItem $item): Response|JsonResponse
    {
        if (null !== $item) {
            $this->repository->remove($item, true);
        }

        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/subtract', name: 'app_cart_item_rem_qua_product', methods: ['PATCH'])]
    public function removeProductFromItem(?CartItem $item): Response|JsonResponse
    {
        if (null === $item) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }
        $this->repository->save($item->subtractOne(), true);

        return $this->json($item);
    }

    #[Route('/{id}/add', name: 'app_cart_item_add_qua_product', methods: ['PATCH'])]
    public function addQuantityProductToItem(?CartItem $item): Response|JsonResponse
    {
        if (null === $item) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }
        $this->repository->save($item->addOne(), true);

        return $this->json($item);
    }
}
