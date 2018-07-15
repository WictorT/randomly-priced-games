<?php
namespace App\Transformer;

use App\DTO\AbstractDTO;
use App\Entity\BaseEntity;

interface TransformerInterface
{
    public function transform(BaseEntity $entity): AbstractDTO;
    public function reverseTransform(AbstractDTO $dto): BaseEntity;
}
