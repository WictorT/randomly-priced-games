<?php
namespace App\Handler;

use App\DTO\BaseDTO;
use App\DTO\UserDTO;
use App\Transformer\UserTransformer;
use Doctrine\ORM\EntityManagerInterface;
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
}
