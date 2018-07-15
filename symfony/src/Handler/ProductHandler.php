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
     * @param BaseDTO|ProductDTO $dto
     * @return BaseEntity|Product
     */
    public function create(BaseDTO $dto): BaseEntity
    {
        $product = $this->transformer->reverseTransform($dto);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    /**
     * @param BaseEntity $entity
     * @param BaseDTO $dto
     * @return BaseEntity
     */
    public function update(BaseEntity $entity, BaseDTO $dto): BaseEntity
    {
        $product = $this->transformer->reverseTransform($dto, $entity);

        $this->entityManager->merge($product);
        $this->entityManager->flush();

        return $product;
    }

    /**
     * @return BaseEntity|ProductRepository
     */
    public function getRepository(): BaseRepository
    {
        return $this->entityManager->getRepository(Product::class);
    }

}
