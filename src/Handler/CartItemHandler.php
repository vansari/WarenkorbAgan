<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;

class CartItemHandler
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CartItemRepository $cartItemRepository
    ) {
    }

    public function createFromItemJson(string $json): CartItem
    {
        return $this->createFromItemArray(json_decode($json, true));
    }

    /**
     * @param array<mixed> $data
     * @return CartItem
     */
    public function createFromItemArray(array $data): CartItem
    {
        $item = new CartItem();

        if (
            null === ($data['product'] ?? null)
            || null === ($product = $this->productRepository->find($data['product']))
        ) {
            return $item;
        }

        return $item
            ->setProduct($product)
            ->setQuantity($data['quantity'] ?? 0);
    }

    public function getCartItem(int $id): ?CartItem
    {
        return $this->cartItemRepository->find($id);
    }
}
