<?php
namespace App\Repository;

use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class UserRepository extends BaseRepository implements UserLoaderInterface
{
    /**
     * @param string $alias
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder(string $alias = 'u'): QueryBuilder
    {
        return $this->createQueryBuilder($alias);
    }

    /**
     * @param string $username
     * @return mixed|null|\Symfony\Component\Security\Core\User\UserInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        return $this->getQueryBuilder()
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
