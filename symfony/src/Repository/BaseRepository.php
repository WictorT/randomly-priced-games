<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;

abstract class BaseRepository extends EntityRepository
{
    abstract function getQueryBuilder();
}
