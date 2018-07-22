<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\ApiTestCase;
use App\Tests\Helper\CartItemHelper;
use App\Tests\Helper\ProductHelper;
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

        $response = $this->performRequest('GET', 'app.cart-items.list');
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
}
