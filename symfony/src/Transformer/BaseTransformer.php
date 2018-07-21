<?php
namespace App\Transformer;

use App\DTO\BaseDTO;
use App\Entity\BaseEntity;

abstract class BaseTransformer
{
    /**
     * @param BaseEntity $entity
     *
     * @return BaseDTO
     */
    abstract public function transform(BaseEntity $entity): BaseDTO;

    /**
     * @param BaseDTO $dto
     * @param BaseEntity|null $entity
     *
     * @return BaseEntity
     */
    abstract public function reverseTransform(BaseDTO $dto, ?BaseEntity $entity): BaseEntity;

    /**
     * @param array|\Iterator $entities
     *
     * @return array
     */
    public function transformMultiple($entities): array
    {
        $dtos = [];

        foreach ($entities as $entity) {
            $dtos[] = $this->transform($entity);
        }

        return $dtos;
    }
}
