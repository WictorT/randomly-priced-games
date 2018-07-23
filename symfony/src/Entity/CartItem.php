<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="rpg_cart_items")
 * @ORM\Entity(repositoryClass="App\Repository\CartItemRepository")
 */
class CartItem extends BaseEntity
{
    public const MAX_ITEMS = 3;
    public const MAX_PRODUCTS_PER_ITEM = 10;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $count;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     *
     * @var Product
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="cartItems")
     *
     * @var User
     */
    private $user;

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return CartItem
     */
    public function setCount(int $count): CartItem
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     *
     * @return CartItem
     */
    public function setProduct(Product $product): CartItem
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @param User $user
     *
     * @return CartItem
     */
    public function setUser(User $user): CartItem
    {
        $this->user = $user;
        return $this;
    }
}
