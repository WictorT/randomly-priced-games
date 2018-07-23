<?php
namespace App\Transformer;

use App\DTO\BaseDTO;
use App\DTO\UserDTO;
use App\Entity\BaseEntity;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserTransformer extends BaseTransformer
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param BaseEntity|User $entity
     *
     * @return BaseDTO|UserDTO
     */
    public function transform(BaseEntity $entity): BaseDTO
    {
        $dto = new UserDTO();

        $dto->id = $entity->getId();
        $dto->username = $entity->getUsername();
        $dto->email = $entity->getEmail();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();

        return $dto;
    }

    /**
     * @param BaseDTO|UserDTO $dto
     * @param BaseEntity|User|null $entity
     *
     * @return BaseEntity|User
     */
    public function reverseTransform(BaseDTO $dto, ?BaseEntity $entity = null): BaseEntity
    {
        $entity = $entity ?: new User();

        $entity
            ->setUsername($dto->username)
            ->setEmail($dto->email);

        $password = $this->encoder->encodePassword($entity, $dto->password);
        $entity->setPassword($password);

        return $entity;
    }
}
