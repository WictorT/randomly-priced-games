<?php
namespace App\Handler;

use App\DTO\BaseDTO;
use App\DTO\CartItemDTO;
use App\Entity\BaseEntity;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\BaseRepository;
use App\Repository\ProductRepository;
use App\Transformer\CartItemTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CartItemHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var CartItemTransformer */
    private $transformer;

    /** @var ProductHandler */
    private $productHandler;

    /**
     * @param EntityManagerInterface $entityManager
     * @param CartItemTransformer $transformer
     * @param ProductHandler $productHandler
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CartItemTransformer $transformer,
        ProductHandler $productHandler
    ) {
        $this->entityManager = $entityManager;
        $this->transformer = $transformer;
        $this->productHandler = $productHandler;
    }

    /**
     * @param BaseEntity|CartItem $cartItem
     * @return BaseDTO|CartItemDTO
     */
    public function getDto(BaseEntity $cartItem): BaseDTO
    {
        return $this->transformer->transform($cartItem);
    }

    /**
     * @param User $user
     * @return array
     */
    public function getAll(User $user) {
        $cartItems = $user->getCartItems();

        return [
            'items' => $this->transformer->transformMultiple($cartItems),
            'total_price' => $this->getTotalPrice($cartItems),
        ];
    }

    /**
     * @param User $user
     * @param CartItemDTO $productDto
     * @return CartItemDTO
     */
    public function addToCart(User $user, CartItemDTO $productDto): CartItemDTO
    {
        $product = $this->productHandler->getById($productDto->productId);

        $cartItems = $user->getCartItems();

        if ($cartItem = $this->getCardItemByProduct($cartItems, $product)) {
            $newCount = min(CartItem::MAX_PRODUCTS_PER_ITEM, $cartItem->getCount() + 1);
            $cartItem->setCount($newCount);
        } elseif ($cartItems->count() < CartItem::MAX_ITEMS) {
            $cartItem = (new CartItem)
                ->setUser($user)
                ->setCount(1)
                ->setProduct($product);

            $cartItems->add($cartItem);
        } else {
            throw new BadRequestHttpException("Maximum " . CartItem::MAX_ITEMS . " items can be added to the cart");
        }

        $this->entityManager->flush();

        return $this->getDto($cartItem);
    }

    /**
     * @param User $user
     * @param CartItemDTO $productDto
     */
    public function removeFromCart(User $user, CartItemDTO $productDto): void
    {
        $product = $this->productHandler->getById($productDto->productId);
        $cartItems = $user->getCartItems();

        if ($cartItem = $this->getCardItemByProduct($cartItems, $product)) {
            $newCount = $cartItem->getCount() - 1;

            if ($newCount === 0) {
                $this->entityManager->remove($cartItem);
            } else {
                $cartItem->setCount($newCount);
            }
        } else {
            throw new BadRequestHttpException("This item does not exist in the cart");
        }

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
