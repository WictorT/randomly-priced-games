<?php

namespace App\Tests\Helper;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductHelper
{
    const DEFAULT_PER_PAGE = 3;
    const DEFAULT_PAGE = 1;
    const TEST_PRODUCT_PRICE = 59.99;
    const TEST_PRODUCT_NAME = 'Cyberpunk 2077';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $name
     * @param float $price
     * @return Product|null|object
     */
    public function createProduct($name = self::TEST_PRODUCT_NAME, $price = self::TEST_PRODUCT_PRICE): Product
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => $name]);

        if ($product) {
            $product->setPrice($price);
            $this->entityManager->merge($product);
        } else {
            $product = (new Product)
                ->setName($name)
                ->setPrice($price);
            $this->entityManager->persist($product);
        }
        $this->entityManager->flush();

        return $product;
    }

    /**
     * @param array $findParams
     * @return void
     */
    public function removeProduct(array $findParams = ['name' => self::TEST_PRODUCT_NAME]): void
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy($findParams);
        $product && $this->entityManager->remove($product);

        $this->entityManager->flush();
    }

    public function removeAllProducts(): void
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        foreach ($products as $product) {
            $product && $this->entityManager->remove($product);
        }

        $this->entityManager->flush();
    }
}
