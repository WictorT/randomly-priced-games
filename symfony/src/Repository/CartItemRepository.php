<?php
namespace App\Repository;

use Doctrine\ORM\QueryBuilder;

class CartItemRepository extends BaseRepository
{
    /**
     * @param string $alias
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder(string $alias = 'ci'): QueryBuilder
    {
        return $this->createQueryBuilder($alias);
    }
}
