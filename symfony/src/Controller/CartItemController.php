<?php
namespace App\Controller;

use App\DTO\CartItemDTO;
use App\Entity\User;
use App\Handler\CartItemHandler;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CartItemController extends Controller
{
    /** @var CartItemHandler $cartItemHandler */
    private $cartItemHandler;

    /**
     * @param CartItemHandler $cartItemHandler
     */
    public function __construct(CartItemHandler $cartItemHandler)
    {
        $this->cartItemHandler = $cartItemHandler;
    }

    /**
     * @Rest\Get(path="user/{id}/cart-items", name="app.user.cart-items.list")
     *
     * @param User $user
     * @return JsonResponse
     */
    public function indexAction(User $user)
    {
        $cartItems = $this->cartItemHandler->getAll($user);

        return $this->json($cartItems, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(path="user/{id}/cart-items/add", name="app.user.cart-items.add")
     * @ParamConverter("cartItemDTO", converter="fos_rest.request_body")
     *
     * @param User $user
     * @param CartItemDTO $cartItemDTO
     * @return JsonResponse
     */
    public function addToCartAction(User $user, CartItemDTO $cartItemDTO) {
        $cartItem = $this->cartItemHandler->addToCart($user, $cartItemDTO);

        return $this->json($cartItem, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(path="user/{id}/cart-items/remove", name="app.user.cart-items.remove")
     * @ParamConverter("cartItemDTO", converter="fos_rest.request_body")
     *
     * @param User $user
     * @param CartItemDTO $cartItemDTO
     * @return JsonResponse
     */
    public function removeFromCartAction(User $user, CartItemDTO $cartItemDTO) {
        $this->cartItemHandler->removeFromCart($user, $cartItemDTO);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
