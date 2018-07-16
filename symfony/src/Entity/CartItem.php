<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="rpg_cart_items")
 * @ORM\Entity(repositoryClass="App\Repository\CartItemRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("name")
 */
class CartItem extends BaseEntity
{
    const MAX_ITEMS = 3;
    const MAX_PRODUCTS_PER_ITEM = 10;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $count;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="cartItems")
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
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
     * @return CartItem
     */
    public function setProduct(Product $product): CartItem
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @param User $user
     * @return CartItem
     */
    public function setUser(User $user): CartItem
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist()
     *
     * @return void
     */
    public function prePersist(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     *
     * @return void
     */
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
