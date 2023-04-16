<?php

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['item:read', ])]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Product::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['item:read'])]
    private ?Product $product = null;

    #[ORM\Column]
    #[GreaterThan(0, groups: [ 'cart:create', 'cart:update', 'item:create', 'item:update',])]
    #[Groups(['cart:create', 'cart:update', 'item:create', 'item:read', 'item:update',])]
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
        return $this->getQuantity() * ($this->getProduct()?->getPrice() ?? 0);
    }

    #[Ignore]
    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    public function equals(self $item): bool
    {
        return $this->getProduct()->getId() === $item->getProduct()->getId();
    }
}
