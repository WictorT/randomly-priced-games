<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="rpg_products")
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @UniqueEntity("name")
 */
class Product extends BaseEntity
{
    /**
     * @ORM\Column(type="string", length=254, unique=true)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", scale=2)
     *
     * @var float
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CartItem", cascade={"remove"}, mappedBy="product")
     */
    private $cartItems;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Product
     */
    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return Product
     */
    public function setPrice(float $price): Product
    {
        $this->price = $price;
        return $this;
    }
}
