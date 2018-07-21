<?php
namespace App\Controller;

use App\DTO\CartItemDTO;
use App\Handler\CartItemHandler;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Rest\Route("/api")
 */
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
     * @Rest\Get(path="/cart-items", name="app.cart-items.list")
     *
     * @return View
     */
    public function indexAction(): View
    {
        $user = $this->getUser();

        $cartItems = $this->cartItemHandler->getAll($user);

        return View::create($cartItems, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(path="/cart-items/add", name="app.cart-items.add")
     * @ParamConverter("cartItemDTO", converter="fos_rest.request_body")
     *
     * @param CartItemDTO $cartItemDTO
     *
     * @return View
     */
    public function addToCartAction(CartItemDTO $cartItemDTO): View
    {
        $user = $this->getUser();

        $cartItem = $this->cartItemHandler->addToCart($user, $cartItemDTO);

        return View::create($cartItem, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Post(path="/cart-items/remove", name="app.cart-items.remove")
     * @ParamConverter("cartItemDTO", converter="fos_rest.request_body")
     *
     * @param CartItemDTO $cartItemDTO
     *
     * @return View
     */
    public function removeFromCartAction(CartItemDTO $cartItemDTO): View
    {
        $user = $this->getUser();

        $this->cartItemHandler->removeFromCart($user, $cartItemDTO);

        return View::create(null, Response::HTTP_NO_CONTENT);
    }
}
