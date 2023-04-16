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
        private readonly EntityManagerInterface $entityManager
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
        /** @var ProductRepository $productRepo */
        $productRepo = $this->entityManager->getRepository(Product::class);
        foreach ($items as $item) {
            $product = $productRepo->find($item['product']);
            if (null === $product) {
                throw new EntityNotFoundException('No product with Id ' . $item['product'] . ' found.');
            }
            $cartItem = (new CartItem())
                ->setQuantity($item['quantity'] ?? 0)
                ->setProduct($product);

            $cart->addItem($cartItem);
        }

        return $cart;
    }
}