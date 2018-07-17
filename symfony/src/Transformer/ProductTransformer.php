<?php
namespace App\Transformer;

use App\DTO\BaseDTO;
use App\DTO\ProductDTO;
use App\Entity\BaseEntity;
use App\Entity\Product;

class ProductTransformer extends BaseTransformer
{
    /**
     * @param BaseEntity|Product $entity
     * @return BaseDTO|ProductDTO
     */
    public function transform(BaseEntity $entity): BaseDTO
    {
        $dto = new ProductDTO();

        $dto->id = $entity->getId();
        $dto->name = $entity->getName();
        $dto->price = $entity->getPrice();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();

        return $dto;
    }

    /**
     * @param BaseDTO|ProductDTO $dto
     * @param BaseEntity|Product|null $entity
     * @return BaseEntity|Product
     */
    public function reverseTransform(BaseDTO $dto, ?BaseEntity $entity = null): BaseEntity
    {
        $entity = $entity ?: new Product();

        return $entity
            ->setName($dto->name)
            ->setPrice($dto->price);
    }
}
