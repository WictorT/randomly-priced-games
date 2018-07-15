<?php
namespace App\Transformer;

use App\DTO\AbstractDTO;
use App\DTO\UserDTO;
use App\Entity\BaseEntity;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserTransformer implements TransformerInterface
{
    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /**
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    /**
     * @param BaseEntity|User $entity
     * @return AbstractDTO|UserDTO
     */
    public function transform(BaseEntity $entity): AbstractDTO
    {
        // TODO: Implement transform() method.
    }

    /**
     * @param AbstractDTO|UserDTO $dto
     * @return BaseEntity|User
     */
    public function reverseTransform(AbstractDTO $dto): BaseEntity
    {
        $user = (new User)
            ->setUsername($dto->username)
            ->setEmail($dto->email);

        $password = $this->encoder->encodePassword($user, $dto->password);
        $user->setPassword($password);

        return $user;
    }
}
