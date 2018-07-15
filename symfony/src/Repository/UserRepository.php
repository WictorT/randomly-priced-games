<?php
namespace App\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class UserRepository extends BaseRepository implements UserLoaderInterface
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder() {
        return $this->createQueryBuilder('u');
    }

    /**
     * @param string $username
     * @return mixed|null|\Symfony\Component\Security\Core\User\UserInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
