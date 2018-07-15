<?php
namespace App\Handler;

use App\DTO\UserDTO;
use App\Entity\BaseEntity;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\BaseRepository;
use App\Repository\ProductRepository;
use App\Transformer\UserTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductHandler extends BaseHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserTransformer */
    private $transformer;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserTransformer $transformer
     * @param UrlGeneratorInterface $router
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserTransformer $transformer,
        UrlGeneratorInterface $router
    ) {
        parent::__construct($router);

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

    /**
     * @return BaseEntity|ProductRepository
     */
    function getRepository(): BaseRepository
    {
        return $this->entityManager->getRepository(Product::class);
    }
}
