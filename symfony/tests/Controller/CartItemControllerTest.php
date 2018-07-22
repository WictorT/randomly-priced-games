<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\ApiTestCase;
use App\Tests\Helper\CartItemHelper;
use App\Tests\Helper\ProductHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartItemControllerTest extends ApiTestCase
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var CartItemHelper
     */
    private $cartItemHelper;

    /**
     * @var ProductHelper
     */
    private $productHelper;

    protected function setUp()
    {
        parent::setUp();

        $this->user = $this->entityManager->getRepository(User::class)->findOneByUsername('admin');
        $this->cartItemHelper = new CartItemHelper($this->user, $this->entityManager);
        $this->productHelper = new ProductHelper($this->entityManager);
    }

    public function testIndexActionSucceeds()
    {
        $this->cartItemHelper->emptyCart();
        $this->cartItemHelper->addCartItem($this->productHelper->createProduct('Gwent', 0.0), 9);
        $this->cartItemHelper->addCartItem($this->productHelper->createProduct('Witcher 3', 49.99), 3);

        $this->performRequest(Request::METHOD_GET, 'app.cart-items.list');
        // called twice to cover the case when total_price comes from redis
        $response = $this->performRequest(Request::METHOD_GET, 'app.cart-items.list');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_OK,
                'content' => [
                    "items" => [
                        [
                            "id" => 'exists',
                            "count" => 9,
                            "product" => [
                                "id" => 'exists',
                                "name" => "Gwent",
                                "price" => 0,
                                "created_at" => 'exists',
                                "updated_at" => 'exists',
                            ],
                            "created_at" => 'exists',
                            "updated_at" => 'exists',
                        ],
                        [
                            "id" => 'exists',
                            "count" => 3,
                            "product" => [
                                "id" => 'exists',
                                "name" => "Witcher 3",
                                "price" => 49.99,
                                "created_at" => 'exists',
                                "updated_at" => 'exists',
                            ],
                            "created_at" => 'exists',
                            "updated_at" => 'exists',
                        ]
                    ],
                    "total_price" => 149.97
                ]
            ],
            [
                'status_code' => $response->getStatusCode(),
                'content' => [
                    "items" => [
                        [
                            "id" => $responseContent->items[0]->id ? 'exists' : 'is missing',
                            "count" => $responseContent->items[0]->count,
                            "product" => [
                                "id" => $responseContent->items[0]->product->id ? 'exists' : 'is missing',
                                "name" => $responseContent->items[0]->product->name,
                                "price" => $responseContent->items[0]->product->price,
                                "created_at" => $responseContent->items[0]->product->created_at ? 'exists' : 'is missing',
                                "updated_at" => $responseContent->items[0]->product->updated_at ? 'exists' : 'is missing',
                            ],
                            "created_at" => $responseContent->items[0]->created_at ? 'exists' : 'is missing',
                            "updated_at" => $responseContent->items[0]->updated_at ? 'exists' : 'is missing',
                        ],
                        [
                            "id" => $responseContent->items[1]->id ? 'exists' : 'is missing',
                            "count" => $responseContent->items[1]->count,
                            "product" => [
                                "id" => $responseContent->items[1]->product->id ? 'exists' : 'is missing',
                                "name" => $responseContent->items[1]->product->name,
                                "price" => $responseContent->items[1]->product->price,
                                "created_at" => $responseContent->items[1]->product->created_at ? 'exists' : 'is missing',
                                "updated_at" => $responseContent->items[1]->product->updated_at ? 'exists' : 'is missing',
                            ],
                            "created_at" => $responseContent->items[1]->created_at ? 'exists' : 'is missing',
                            "updated_at" => $responseContent->items[1]->updated_at ? 'exists' : 'is missing',
                        ],
                    ],
                    "total_price" => $responseContent->total_price
                ]
            ]
        );
    }

    public function testAddToCartActionSucceedsOnEmptyCard()
    {
        $this->cartItemHelper->emptyCart();
        $product = $this->productHelper->createProduct('Witcher 3', 49.99);

        $response = $this->performRequest(Request::METHOD_POST, 'app.cart-items.add', [], [
            'product_id' => $product->getId(),
        ]);
        $responseContent = json_decode($response->getContent());

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_CREATED,
                'content' => [
                    "id" => 'exists',
                    "count" => 1,
                    "product" => [
                        "id" => $product->getId(),
                        "name" => "Witcher 3",
                        "price" => 49.99,
                        "created_at" => 'exists',
                        "updated_at" => 'exists',
                    ],
                    "created_at" => 'exists',
                    "updated_at" => 'exists',
                ]
            ],
            [
                'status_code' => $response->getStatusCode(),
                'content' => [
                    "id" => $responseContent->id ? 'exists' : 'is missing',
                    "count" => $responseContent->count,
                    "product" => [
                        "id" => $responseContent->product->id,
                        "name" => $responseContent->product->name,
                        "price" => $responseContent->product->price,
                        "created_at" => $responseContent->product->created_at ? 'exists' : 'is missing',
                        "updated_at" => $responseContent->product->updated_at ? 'exists' : 'is missing',
                    ],
                    "created_at" => $responseContent->created_at ? 'exists' : 'is missing',
                    "updated_at" => $responseContent->updated_at ? 'exists' : 'is missing',
                ]
            ]
        );
    }

    public function testAddToCartActionSucceedsOnMaxItemCount()
    {
        $this->cartItemHelper->emptyCart();
        $product = $this->productHelper->createProduct('Witcher 3', 49.99);
        $this->cartItemHelper->addCartItem($product, 10);

        $response = $this->performRequest(Request::METHOD_POST, 'app.cart-items.add', [], [
            'product_id' => $product->getId(),
        ]);
        $responseContent = json_decode($response->getContent());

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_CREATED,
                'content' => [
                    "id" => 'exists',
                    "count" => 10,
                    "product" => [
                        "id" => $product->getId(),
                        "name" => "Witcher 3",
                        "price" => 49.99,
                        "created_at" => 'exists',
                        "updated_at" => 'exists',
                    ],
                    "created_at" => 'exists',
                    "updated_at" => 'exists',
                ]
            ],
            [
                'status_code' => $response->getStatusCode(),
                'content' => [
                    "id" => $responseContent->id ? 'exists' : 'is missing',
                    "count" => $responseContent->count,
                    "product" => [
                        "id" => $responseContent->product->id,
                        "name" => $responseContent->product->name,
                        "price" => $responseContent->product->price,
                        "created_at" => $responseContent->product->created_at ? 'exists' : 'is missing',
                        "updated_at" => $responseContent->product->updated_at ? 'exists' : 'is missing',
                    ],
                    "created_at" => $responseContent->created_at ? 'exists' : 'is missing',
                    "updated_at" => $responseContent->updated_at ? 'exists' : 'is missing',
                ]
            ]
        );
    }

    public function testAddToCartActionReturnsNotFound()
    {
        $this->productHelper->removeProduct(['id' => 2077]);

        $response = $this->performRequest(Request::METHOD_POST, 'app.cart-items.add', [], [
            'product_id' => 2077,
        ]);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testAddToCartActionReturnsBadRequest()
    {
        $this->cartItemHelper->emptyCart();
        $this->cartItemHelper->addCartItem($this->productHelper->createProduct('Gwent', 0.0), 9);
        $this->cartItemHelper->addCartItem($this->productHelper->createProduct('Witcher 3', 49.99), 3);
        $this->cartItemHelper->addCartItem($this->productHelper->createProduct('AC: Origins', 2.99), 4);
        $product = $this->productHelper->createProduct('MGSV:PP', 22.99);

        $response = $this->performRequest(Request::METHOD_POST, 'app.cart-items.add', [], [
            'product_id' => $product->getId(),
        ]);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testRemoveFromCartActionSucceedsForUniqueItem()
    {
        $product = $this->productHelper->createProduct('Witcher 3', 49.99);
        $this->cartItemHelper->emptyCart();
        $this->cartItemHelper->addCartItem($product, 1);

        $response = $this->performRequest(Request::METHOD_POST, 'app.cart-items.remove', [], [
            'product_id' => $product->getId(),
        ]);

        // Reload user (workaround)
        $this->user = $this->entityManager->find(User::class, $this->user->getId());

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_NO_CONTENT,
                'cart_items_count' => 0,
            ],
            [
                'status_code' => $response->getStatusCode(),
                'cart_items_count' => $this->user->getCartItems()->count(),
            ]
        );
    }

    public function testRemoveFromCartActionSucceedsForNonUniqueItem()
    {
        $product = $this->productHelper->createProduct('Witcher 3', 49.99);
        $this->cartItemHelper->emptyCart();
        $this->cartItemHelper->addCartItem($product, 3);

        $response = $this->performRequest(Request::METHOD_POST, 'app.cart-items.remove', [], [
            'product_id' => $product->getId(),
        ]);

        // Reload user (workaround)
        $this->user = $this->entityManager->find(User::class, $this->user->getId());

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_NO_CONTENT,
                'cart_item_count' => 2,
            ],
            [
                'status_code' => $response->getStatusCode(),
                'cart_item_count' => $this->user->getCartItems()->first()->getCount(),
            ]
        );
    }

    public function testRemoveFromCartActionReturnsNotFound()
    {
        $this->productHelper->removeProduct(['id' => 2077]);

        $response = $this->performRequest(Request::METHOD_POST, 'app.cart-items.remove', [], [
            'product_id' => 2077,
        ]);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testRemoveFromCartActionReturnsBadRequest()
    {
        $this->cartItemHelper->emptyCart();
        $product = $this->productHelper->createProduct('MGSV:PP', 22.99);

        $response = $this->performRequest(Request::METHOD_POST, 'app.cart-items.remove', [], [
            'product_id' => $product->getId(),
        ]);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
