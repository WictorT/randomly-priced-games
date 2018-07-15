<?php
namespace App\Repository;

class ProductRepository extends BaseRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder() {
        return $this->createQueryBuilder('p');
    }
}
