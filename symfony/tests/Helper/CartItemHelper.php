<?php

namespace App\Tests\Helper;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CartItemHelper
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(User $user, EntityManagerInterface $entityManager)
    {
        $this->user = $user;
        $this->entityManager = $entityManager;
    }

    public function emptyCart()
    {
        $cartItems = $this->user->getCartItems();

        foreach ($cartItems as $cartItem) {
            $this->entityManager->remove($cartItem);
        }

        $this->entityManager->flush();
    }

    /**
     * @param Product $product
     * @param int $count
     */
    public function addCartItem(Product $product, int $count = 1)
    {
        $cartItems = $this->user->getCartItems();
        $cartItem = (new CartItem)
            ->setProduct($product)
            ->setCount($count)
            ->setUser($this->user);

        $cartItems->add($cartItem);
        $this->user->setCartItems($cartItems);

        $this->entityManager->persist($this->user);
        $this->entityManager->flush();
    }
}
