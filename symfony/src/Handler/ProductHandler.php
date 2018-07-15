<?php
namespace App\Handler;

use App\DTO\BaseDTO;
use App\DTO\ProductDTO;
use App\Entity\BaseEntity;
use App\Entity\Product;
use App\Repository\BaseRepository;
use App\Repository\ProductRepository;
use App\Transformer\ProductTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductHandler extends BaseHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ProductTransformer */
    private $transformer;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ProductTransformer $transformer
     * @param UrlGeneratorInterface $router
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ProductTransformer $transformer,
        UrlGeneratorInterface $router
    ) {
        parent::__construct($router);

        $this->entityManager = $entityManager;
        $this->transformer = $transformer;
    }

    /**
     * @param BaseDTO|ProductDTO $productDto
     * @return BaseEntity|Product
     */
    public function create(BaseDTO $productDto): BaseEntity
    {
        $product = $this->transformer->reverseTransform($productDto);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    /**
     * @param BaseEntity $product
     * @param BaseDTO $productDto
     * @return BaseEntity
     */
    public function update(BaseEntity $product, BaseDTO $productDto): BaseEntity
    {
        $product = $this->transformer->reverseTransform($productDto, $product);

        $this->entityManager->merge($product);
        $this->entityManager->flush();

        return $product;
    }

    /**
     * @param BaseEntity|Product $product
     * @return void
     */
    public function delete(BaseEntity $product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    /**
     * @return BaseEntity|ProductRepository
     */
    public function getRepository(): BaseRepository
    {
        return $this->entityManager->getRepository(Product::class);
    }
}
