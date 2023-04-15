<?php

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private int $quantity = 0;

    #[ORM\ManyToOne(inversedBy: 'items')]
    private ?Cart $cart = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
        $this->setQuantity(1);

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function addOne(): self
    {
        $this->setQuantity(($this->getQuantity() + 1));
        return $this;
    }

    public function subtractOne(): self
    {
        if (0 < $this->getQuantity()) {
            $this->setQuantity(($this->getQuantity() - 1));
        }

        return $this;
    }

    public function getTotal(): float
    {
        return 0 === $this->getQuantity()
            ? 0
            : $this->getQuantity() * $this->getProduct()->getPrice();
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }
}
