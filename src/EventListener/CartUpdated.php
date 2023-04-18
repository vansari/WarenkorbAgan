<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Cart;
use App\Entity\CartItem;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;

#[AsDoctrineListener(event: Events::onFlush, priority: 500, connection: 'default')]
class CartUpdated
{
    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $manager = $eventArgs->getObjectManager();
        /** @var UnitOfWork $uow */
        $uow = $manager->getUnitOfWork();
        /** @var Cart[] $carts */
        $carts = [];
        /** @var ArrayCollection $collectionUpdate */
        foreach ($uow->getScheduledCollectionUpdates() as $collectionUpdate) {
            $carts = array_merge(
                $carts,
                array_filter(
                    array_map(
                        fn (object $entity): ?Cart => $this->isNewChildOfExistingParent($entity),
                        $collectionUpdate->toArray()
                    )
                )
            );
        }
        /** @var ArrayCollection $collectionDeletion */
        foreach ($uow->getScheduledCollectionDeletions() as $collectionDeletion) {
            $carts = array_merge(
                $carts,
                array_filter(
                    array_map(
                        fn (object $entity): ?Cart => $this->isNewChildOfExistingParent($entity),
                        $collectionDeletion->toArray()
                    )
                )
            );
        }

        if (empty($carts)) {
            return;
        }

        foreach ($this->removeDuplicates($carts) as $cart) {
            $cart->setUpdatedAt();
        }
    }

    private function isNewChildOfExistingParent(object $entity): ?Cart
    {
        return $entity instanceof CartItem && $entity->getCart()?->getId()
            ? $entity->getCart()
            : null;
    }

    /**
     * @param array<Cart> $carts
     * @return Cart[]
     */
    private function removeDuplicates(array $carts): array
    {
        $filtered = [];
        /** @var Cart $cart */
        foreach ($carts as $cart) {
            if (array_key_exists($cart->getId(), $filtered)) {
                continue;
            }
            $filtered[$cart->getId()] = $cart;
        }

        return $filtered;
    }
}
