<?php
namespace App\Transformer;

use App\DTO\BaseDTO;
use App\Entity\BaseEntity;

interface TransformerInterface
{
    /**
     * @param BaseEntity $entity
     * @return BaseDTO
     */
    public function transform(BaseEntity $entity): BaseDTO;

    /**
     * @param BaseDTO $dto
     * @param BaseEntity|null $entity
     * @return BaseEntity
     */
    public function reverseTransform(BaseDTO $dto, ?BaseEntity $entity): BaseEntity;
}
