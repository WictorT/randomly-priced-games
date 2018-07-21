<?php
namespace App\Repository;

use Doctrine\ORM\QueryBuilder;

class ProductRepository extends BaseRepository
{
    /**
     * @param string $alias
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder(string $alias = 'p'): QueryBuilder
    {
        return $this->createQueryBuilder($alias);
    }
}
