<?php
namespace App\Transformer;

use App\DTO\BaseDTO;
use App\DTO\CartItemDTO;
use App\Entity\BaseEntity;
use App\Entity\CartItem;

class CartItemTransformer extends BaseTransformer
{
    /** @var ProductTransformer */
    private $productTransformer;

    public function __construct(ProductTransformer $productTransformer)
    {
        $this->productTransformer = $productTransformer;
    }

    /**
     * @param BaseEntity|CartItem $entity
     * @return BaseDTO|CartItemDTO
     */
    public function transform(BaseEntity $entity): BaseDTO
    {
        $dto = new CartItemDTO();

        $dto->id = $entity->getId();
        $dto->count = $entity->getCount();
        $dto->product = $this->productTransformer->transform($entity->getProduct());
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();

        return $dto;
    }

    /**
     * @param BaseDTO|CartItemDTO $dto
     * @param BaseEntity|CartItem|null $entity
     * @return BaseEntity|CartItem
     */
    public function reverseTransform(BaseDTO $dto, ?BaseEntity $entity = null): BaseEntity
    {
        // TODO: Implement reverseTransform() method.
    }
}
