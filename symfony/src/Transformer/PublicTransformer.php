<?php
namespace App\Transformer;

use App\DTO\AbstractDTO;
use App\DTO\ProductDTO;
use App\Entity\BaseEntity;
use App\Entity\Product;

class PublicTransformer implements TransformerInterface
{
    /**
     * @param BaseEntity|Product $entity
     * @return AbstractDTO|ProductDTO
     */
    public function transform(BaseEntity $entity): AbstractDTO
    {
        // TODO: Implement transform() method.
    }

    /**
     * @param AbstractDTO|ProductDTO $dto
     * @return BaseEntity|Product
     */
    public function reverseTransform(AbstractDTO $dto): BaseEntity
    {
        $entity = (new Product)
            ->setName($dto->name)
            ->setPrice($dto->price);

        return $entity;
    }
}
