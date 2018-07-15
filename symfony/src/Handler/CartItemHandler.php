<?php
namespace App\Handler;

use App\DTO\BaseDTO;
use App\DTO\CartItemDTO;
use App\DTO\ProductDTO;
use App\Entity\BaseEntity;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\BaseRepository;
use App\Repository\ProductRepository;
use App\Transformer\ProductTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CartItemHandler extends BaseHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ProductTransformer */
    private $transformer;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ProductTransformer $transformer
     * @param UrlGeneratorInterface $router
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ProductTransformer $transformer,
        UrlGeneratorInterface $router
    ) {
        parent::__construct($router);

        $this->entityManager = $entityManager;
        $this->transformer = $transformer;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getAll(User $user) {
        $cartItems = $user->getCartItems();

        return [
            'items' => $cartItems,
            'total_price' => $this->getTotalPrice($cartItems),
        ];
    }

    /**
     * @param User $user
     * @param CartItemDTO $productDto
     * @return CartItem
     */
    public function addToCart(User $user, CartItemDTO $productDto): CartItem
    {
        $product = $this->entityManager->getRepository(Product::class)->find($productDto->productId);
        if ($product === null) {
            throw new NotFoundHttpException('Product with this id does not exist');
        }

        $cartItems = $user->getCartItems();

        if ($cartItem = $this->getCardItemByProduct($cartItems, $product)) {
            $newCount = min(10, $cartItem->getCount() + 1);
            $cartItem->setCount($newCount);
        } else {
            $cartItem = (new CartItem)
                ->setUser($user)
                ->setCount(1)
                ->setProduct($product);

            $cartItems->add($cartItem);
        }

        $this->entityManager->flush();

        return $cartItem;
    }

    /**
     * @param User $user
     * @param CartItemDTO $productDto
     */
    public function removeFromCart(User $user, CartItemDTO $productDto): void
    {
        $product = $this->entityManager->getRepository(Product::class)->find($productDto->productId);
        if ($product === null) {
            throw new NotFoundHttpException('Product with this id does not exist');
        }

        $cartItems = $user->getCartItems();

        if ($cartItem = $this->getCardItemByProduct($cartItems, $product)) {
            $newCount = $cartItem->getCount() - 1;

            if ($newCount === 0) {
                $this->entityManager->remove($cartItem);
                return;
            }

            $cartItem->setCount($newCount);
        } else {
            throw new BadRequestHttpException("This item does not exist in the cart");
        }

        $this->entityManager->flush();
    }

    /**
     * @param BaseDTO|ProductDTO $productDto
     * @return BaseEntity|Product
     */
    public function create(BaseDTO $productDto): BaseEntity
    {
        $product = $this->transformer->reverseTransform($productDto);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    /**
     * @param BaseEntity $product
     * @param BaseDTO $productDto
     * @return BaseEntity
     */
    public function update(BaseEntity $product, BaseDTO $productDto): BaseEntity
    {
        $product = $this->transformer->reverseTransform($productDto, $product);

        $this->entityManager->flush();

        return $product;
    }

    /**
     * @param BaseEntity|Product $product
     * @return void
     */
    public function delete(BaseEntity $product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    /**
     * @return BaseEntity|ProductRepository
     */
    public function getRepository(): BaseRepository
    {
        return $this->entityManager->getRepository(CartItem::class);
    }

    /**
     * @param CartItem[] $cartItems
     * @return float
     */
    private function getTotalPrice($cartItems): float
    {
        // TODO cache result with redis
        $totalPrice = 0.0;

        foreach ($cartItems as $cartItem) {
            $totalPrice += $cartItem->getProduct()->getPrice() * $cartItem->getCount();
        }

        return $totalPrice;
    }

    /**
     * @param CartItem[] $cartItems
     * @param Product $product
     * @return CartItem|null
     */
    private function getCardItemByProduct($cartItems, Product $product): ?CartItem
    {
        foreach ($cartItems as $cartItem) {
            if ($cartItem->getProduct() === $product) {
                return $cartItem;
            }
        }

        return null;
    }
}
