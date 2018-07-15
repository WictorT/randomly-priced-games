<?php
namespace App\Handler;


use App\DTO\UserDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;


class UserHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(UserDTO $userDTO): User
    {
        (new User)
            ->setUsername($userDTO->username)
            ->setEmail($userDTO->email)
            ->setUsername($userDTO->username)
            ->setUsername($userDTO->username)
            ->setUsername($userDTO->username)
//        $entityManager = $this->getDoctrine()->getManager();
//        $entityManager->persist($user);
//        $entityManager->flush();
    }
}
