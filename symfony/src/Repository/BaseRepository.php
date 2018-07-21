<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class BaseRepository extends EntityRepository
{
    /**
     * @param string $alias
     *
     * @return mixed
     */
    abstract public function getQueryBuilder(string $alias): QueryBuilder;
}
