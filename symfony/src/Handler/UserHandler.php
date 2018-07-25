<?php
namespace App\Handler;

use App\DTO\BaseDTO;
use App\DTO\UserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Transformer\UserTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserHandler extends BaseHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserTransformer
     */
    private $transformer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserTransformer $transformer
     * @param ValidatorInterface $validator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserTransformer $transformer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->transformer = $transformer;
        $this->validator = $validator;
    }

    /**
     * @param UserDTO $userDTO
     * @return BaseDTO
     */
    public function create(UserDTO $userDTO): BaseDTO
    {
        $user = $this->transformer->reverseTransform($userDTO);

        $validationErrors = $this->validator->validate($user);
        $this->handleValidationErrors($validationErrors);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->transformer->transform($user);
    }

    /**
     * @return UserRepository
     */
    public function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(User::class);
    }
}
