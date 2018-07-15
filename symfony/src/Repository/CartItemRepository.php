<?php
namespace App\Repository;

class CartItemRepository extends BaseRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder() {
        return $this->createQueryBuilder('ci');
    }
}
