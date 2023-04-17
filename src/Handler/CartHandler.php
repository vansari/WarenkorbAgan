<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class CartHandler
{
    public function __construct(
        private readonly CartItemHandler $cartItemHandler
    ) {
    }

    public function createFromCartJson(string $json): ?Cart
    {
        $data = json_decode($json, true);
        if (!array_key_exists('items', $data)) {
            return new Cart();
        }
        $items = $data['items'];
        $cart = new Cart();
        foreach ($items as $item) {
            $cartItem = $this->createFromItemArray($item);

            $cart->addItem($cartItem);
        }

        return $cart;
    }

    /**
     * @param array<mixed> $data
     * @return CartItem
     */
    public function createFromItemArray(array $data): CartItem
    {
        return $this->cartItemHandler->createFromItemArray($data);
    }

    public function createFromItemJson(string $json): CartItem
    {
        return $this->cartItemHandler->createFromItemJson($json);
    }

    public function getCartItemFromId(int $itemId): ?CartItem
    {
        return $this->cartItemHandler->getCartItem($itemId);
    }
}
