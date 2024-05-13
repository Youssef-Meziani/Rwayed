<?php

namespace App\OrderManager;

use App\Entity\Adherent;

final class OrderManager
{
    private array $items;
    private ?Adherent $adherent = null;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function getLines()
    {
        return $this->items;
    }

    /**
     * Get the value of items.
     */
    //    public function getLigne()
    //    {
    //        return $this->items;
    //    }

    /**
     * Set the value of items.
     *
     * @return self
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get the value of user.
     */
    public function getAdherent()
    {
        return $this->adherent;
    }

    /**
     * Set the value of user.
     *
     * @return self
     */
    public function setAdherent(?Adherent $adherent = null)
    {
        $this->adherent = $adherent;

        return $this;
    }

    public function vider()
    {
        return $this->setItems([]);
    }

    public function findItem(OrderItemManager $object)
    {
        if (array_key_exists($object->getId(), $this->items)) {
            return $object->getId();
        }

        return null;
    }

    public function addItem(OrderItemManager $object)
    {
        $key = $object->getId().($object->isWithRepair() ? '_repaired' : '_not_repaired');
        if (array_key_exists($key, $this->items)) {
            $this->items[$key]->increaseQuantity($object->getQuantity());
        } else {
            $this->items[$key] = $object;
        }
    }

    public function updateItem(OrderItemManager $requestedCartItem, OrderItemManager $exsistingCartItem, ?int $index): OrderItemManager
    {
        if (null === $index) {
            return $requestedCartItem;
        }

        return new OrderItemManager($index,
            $exsistingCartItem->getImage(),
            $exsistingCartItem->getPrix(),
            $requestedCartItem->getQuantity() + $exsistingCartItem->getQuantity(),
            $exsistingCartItem->isWithRepair(),
            $exsistingCartItem->getMarque(),
            $exsistingCartItem->getSlug());
    }

    public function modifyItem(int $id, int $quantity, bool $isRepair)
    {
        $key = $id.($isRepair ? '_repaired' : '_not_repaired');
        if (array_key_exists($key, $this->items)) {
            $this->items[$key]->setQuantity($quantity);
        } else {
            throw new \InvalidArgumentException("Item with ID $id and repair status ".($isRepair ? 'with' : 'without').' repair does not exist in the cart.');
        }
    }

    public function removeItem(int $id, bool $isRepair)
    {
        $key = $id.($isRepair ? '_repaired' : '_not_repaired');
        if (array_key_exists($key, $this->items)) {
            unset($this->items[$key]);
        }
    }

    public function removeItemByKey($key): void
    {
        unset($this->items[$key]);
    }
}
