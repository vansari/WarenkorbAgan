<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Handler\CartItemHandler;
use App\Repository\CartItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/items')]
class CartItemController extends AbstractController
{
    public function __construct(
        private readonly CartItemRepository $repository,
        private readonly CartItemHandler $cartItemHandler
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
    public function createItem(Request $request, ValidatorInterface $validator): Response|JsonResponse
    {
        $cartItem = $this->cartItemHandler->createFromItemJson($request->getContent());

        if (($violations = $validator->validate($cartItem, groups: ['item:create']))->count()) {
            return new Response($violations, status: Response::HTTP_BAD_REQUEST);
        }

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
