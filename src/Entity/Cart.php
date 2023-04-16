<?php

namespace App\Entity;

use App\Repository\CartRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: CartRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: CartItem::class, cascade: ['persist', 'remove'], fetch: 'EAGER', orphanRemoval: true)]
    #[Groups(['cart:create', 'cart:update'])]
    #[Count(min: 1, groups: ['cart:create', 'cart:update'])]
    private Collection $items;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(CartItem $item): self
    {
        /** @var CartItem[] $existingItem */
        foreach ($this->getItems() as $existingItem) {
            if ($existingItem->equals($item)) {
                $existingItem->setQuantity($existingItem->getQuantity() + $item->getQuantity());
                return $this;
            }
        }

        $this->items->add($item);
        $item->setCart($this);

        return $this;
    }

    public function removeItem(CartItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getCart() === $this) {
                $item->setCart(null);
            }
        }

        return $this;
    }

    public function removeItemById(int $id): self
    {
        foreach ($this->getItems() as $item) {
            if ($item->getId() === $id) {
                return $this->removeItem($item);
            }
        }

        return $this;
    }

    public function removeItems(): self
    {
        foreach ($this->getItems() as $item) {
            $this->removeItem($item);
        }

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): self
    {
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getTotal(): float
    {
        return array_sum(
            array_map(
                fn (CartItem $item): float => $item->getTotal(),
                $this->getItems()->toArray()
            )
        );
    }
}
