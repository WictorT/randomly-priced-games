<?php
namespace App\Handler;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Transformer\UserTransformer;
use Doctrine\ORM\EntityManagerInterface;

class UserHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserTransformer */
    private $transformer;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserTransformer $transformer
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserTransformer $transformer
    ) {
        $this->entityManager = $entityManager;
        $this->transformer = $transformer;
    }

    /**
     * @param UserDTO $userDTO
     * @return User
     */
    public function create(UserDTO $userDTO): User
    {
        $user = $this->transformer->reverseTransform($userDTO);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
