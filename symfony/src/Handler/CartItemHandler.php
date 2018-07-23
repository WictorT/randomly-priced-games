<?php
namespace App\Handler;

use App\DTO\BaseDTO;
use App\DTO\CartItemDTO;
use App\Entity\BaseEntity;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\BaseRepository;
use App\Repository\CartItemRepository;
use App\Transformer\CartItemTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Predis\Client;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CartItemHandler extends BaseHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CartItemTransformer
     */
    private $transformer;

    /**
     * @var ProductHandler
     */
    private $productHandler;

    /**
     * @var Client
     */
    private $cache;

    /**
     * @param EntityManagerInterface $entityManager
     * @param CartItemTransformer $transformer
     * @param ProductHandler $productHandler
     * @param Client $cache
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CartItemTransformer $transformer,
        ProductHandler $productHandler,
        Client $cache
    ) {
        $this->entityManager = $entityManager;
        $this->transformer = $transformer;
        $this->productHandler = $productHandler;
        $this->cache = $cache;
    }

    /**
     * @param BaseEntity|CartItem $cartItem
     *
     * @return BaseDTO|CartItemDTO
     */
    public function getDto(BaseEntity $cartItem): BaseDTO
    {
        return $this->transformer->transform($cartItem);
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getAll(User $user): array
    {
        $cartItems = $user->getCartItems();

        return [
            'items' => $this->transformer->transformMultiple($cartItems),
            'total_price' => $this->getTotalCartPriceByUser($user),
        ];
    }

    /**
     * @param User $user
     * @param CartItemDTO $productDto
     *
     * @throws BadRequestHttpException
     *
     * @return CartItemDTO
     */
    public function addToCart(User $user, CartItemDTO $productDto): CartItemDTO
    {
        $product = $this->productHandler->getById($productDto->productId);

        $cartItems = $user->getCartItems();
        $cartItem = $this->getCardItemByProduct($cartItems, $product);

        if ($cartItem) {
            $this->increaseCount($cartItem);
        } else {
            $cartItem = $this->createNewCartItem($product, $user);
        }

        $this->entityManager->flush();
        $this->cache->del([$this->getTotalPriceCacheKeyForUser($user)]);

        return $this->getDto($cartItem);
    }

    /**
     * @param User $user
     * @param CartItemDTO $productDto
     *
     * @throws BadRequestHttpException
     */
    public function removeFromCart(User $user, CartItemDTO $productDto): void
    {
        $product = $this->productHandler->getById($productDto->productId);

        $cartItems = $user->getCartItems();
        $cartItem = $this->getCardItemByProduct($cartItems, $product);

        if ($cartItem) {
            $this->decreaseCount($cartItem);
        } else {
            throw new BadRequestHttpException('This item does not exist in the cart');
        }

        $this->entityManager->flush();
        $this->cache->del([$this->getTotalPriceCacheKeyForUser($user)]);
    }

    /**
     * @return CartItemRepository
     */
    public function getRepository(): BaseRepository
    {
        return $this->entityManager->getRepository(CartItem::class);
    }

    /**
     * @param User $user
     *
     * @return float
     */
    private function getTotalCartPriceByUser(User $user): float
    {
        $totalPriceRedisKey = $this->getTotalPriceCacheKeyForUser($user);
        $cachedValue = $this->cache->get($totalPriceRedisKey);
        if ($cachedValue) {
            return $cachedValue;
        }

        $cartItems = $user->getCartItems();
        $totalPrice = 0.0;

        foreach ($cartItems as $cartItem) {
            $totalPrice += $cartItem->getProduct()->getPrice() * $cartItem->getCount();
        }

        $this->cache->set($totalPriceRedisKey, $totalPrice);

        return $totalPrice;
    }

    /**
     * @param CartItem[] $cartItems
     * @param Product $product
     *
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

    /**
     * @param User $user
     *
     * @return string
     */
    private function getTotalPriceCacheKeyForUser(User $user): string
    {
        return User::TOTAL_CART_PRICE_KEY_PREFIX . $user->getId();
    }

    /**
     * @param CartItem $cartItem
     *
     * @return CartItem
     */
    private function increaseCount(CartItem $cartItem): CartItem
    {
        $newCount = min(CartItem::MAX_PRODUCTS_PER_ITEM, $cartItem->getCount() + 1);
        $cartItem->setCount($newCount);

        return $cartItem;
    }

    /**
     * @param CartItem $cartItem
     *
     * @return CartItem
     */
    private function decreaseCount(CartItem $cartItem): CartItem
    {
        $newCount = $cartItem->getCount() - 1;

        if ($newCount === 0) {
            $this->entityManager->remove($cartItem);
        } else {
            $cartItem->setCount($newCount);
        }

        return $cartItem;
    }

    /**
     * @param Product $product
     * @param User $user
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     * @return CartItem|null
     */
    private function createNewCartItem(Product $product, User $user): ?CartItem
    {
        $cartItems = $user->getCartItems();

        if ($cartItems->count() < CartItem::MAX_ITEMS) {
            $cartItem = (new CartItem)
                ->setUser($user)
                ->setCount(1)
                ->setProduct($product);

            $cartItems->add($cartItem);

            return $cartItem;
        } else {
            throw new BadRequestHttpException('Maximum ' . CartItem::MAX_ITEMS . ' items can be added to the cart');
        }
    }
}
