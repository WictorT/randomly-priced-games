<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Tests\ApiTestCase;
use App\Tests\Helper\ProductHelper;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends ApiTestCase
{
    /**
     * @var ProductHelper $productHelper
     */
    private $productHelper;

    protected function setUp()
    {
        parent::setUp();

        $this->productHelper = new ProductHelper($this->entityManager);

        $this->productHelper->createProduct();
    }

    public function testIndexActionSucceeds(): void
    {
        $response = $this->performRequest('GET', 'app.products.list', [], [], false);
        $responseContent = json_decode($response->getContent());

        $products = $this->entityManager->getRepository(Product::class)->findAll();
        $productsCount = \count($products);
        $pagesCount = (int)($productsCount / ProductHelper::DEFAULT_PER_PAGE) +
            (bool)($productsCount % ProductHelper::DEFAULT_PER_PAGE);
        $pageCount = min(ProductHelper::DEFAULT_PER_PAGE, $productsCount);

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_OK,
                'content' => [
                    'page' => ProductHelper::DEFAULT_PAGE,
                    'per_page' => ProductHelper::DEFAULT_PER_PAGE,
                    'page_count' => $pageCount,
                    'total_pages' => $pagesCount,
                    'total_count' => $productsCount,
                    'links' => [
                        'self' => $this->router->generate('app.products.list', ['page' => 1, 'per_page' => 3]),
                        'first' => $this->router->generate('app.products.list', ['page' => 1, 'per_page' => 3]),
                        'last' => $this->router->generate('app.products.list', ['page' => $pagesCount, 'per_page' => 3]),
                        'next' => $this->router->generate('app.products.list', ['page' => 2, 'per_page' => 3]),
                    ],
                    'data_count' => $pageCount,
                ]
            ],
            [
                'status_code' => $response->getStatusCode(),
                'content' => [
                    'page' => $responseContent->page,
                    'per_page' => $responseContent->per_page,
                    'page_count' => $responseContent->page_count,
                    'total_pages' => $responseContent->total_pages,
                    'total_count' => $responseContent->total_count,
                    'links' => [
                        'self' => $responseContent->links->self,
                        'first' => $responseContent->links->first,
                        'last' => $responseContent->links->last,
                        'next' => $responseContent->links->next,
                    ],
                    'data_count' => \count($responseContent->data),
                ]
            ]
        );
    }

    public function testGetActionSuccess(): void
    {
        $product = $this->productHelper->createProduct();

        $response = $this->performRequest('GET', 'app.products.get', ['id' => $product->getId()], [], false);
        $responseContent = json_decode($response->getContent());

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_OK,
                'content' => [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'created_at' => 'exists',
                    'updated_at' => 'exists',
                ]
            ],
            [
                'status_code' => $response->getStatusCode(),
                'content' => [
                    'id' => $responseContent->id,
                    'name' => $responseContent->name,
                    'price' => $responseContent->price,
                    'created_at' => $responseContent->created_at ? 'exists' : 'is missing',
                    'updated_at' => $responseContent->updated_at ? 'exists' : 'is missing',
                ]
            ]
        );
    }

    public function testGetActionReturnsNotFound(): void
    {
        $this->productHelper->removeProduct(['id' => 2077]);

        $response = $this->performRequest('GET', 'app.products.get', ['id' => 2077], [], false);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCreateActionSucceeds(): void
    {
        $this->productHelper->removeProduct();

        $response = $this->performRequest(
            'POST',
            'app.products.create',
            [],
            [
                'name' => ProductHelper::TEST_PRODUCT_NAME,
                'price' => ProductHelper::TEST_PRODUCT_PRICE,
            ]
        );
        $responseContent = json_decode($response->getContent());
        $product = $this->entityManager->getRepository(Product::class)->findOneBy([
            'name' => ProductHelper::TEST_PRODUCT_NAME
        ]);

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_CREATED,
                'content' => [
                    'id' => $product->getId(),
                    'name' => ProductHelper::TEST_PRODUCT_NAME,
                    'price' => ProductHelper::TEST_PRODUCT_PRICE,
                    'created_at' => $product->getCreatedAt()->format(\DateTime::ATOM),
                    'updated_at' => $product->getUpdatedAt()->format(\DateTime::ATOM),
                ]
            ],
            [
                'status_code' => $response->getStatusCode(),
                'content' => [
                    'id' => $responseContent->id,
                    'name' => $responseContent->name,
                    'price' => $responseContent->price,
                    'created_at' => $responseContent->created_at,
                    'updated_at' => $responseContent->updated_at,
                ]
            ]
        );
    }

    /**
     * @dataProvider dataTestCreateActionReturnsBadRequest
     * @param array $data
     */
    public function testCreateActionReturnsBadRequest(array $data): void
    {
        $response = $this->performRequest('POST', 'app.products.create', [], $data);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @return array
     */
    public function dataTestCreateActionReturnsBadRequest() : array
    {
        return [
            'case 1: no parameters' => [
                'data' => [],
            ],
            'case 2: no name' => [
                'data' => [
                    'price' => ProductHelper::TEST_PRODUCT_PRICE,
                ],
            ],
            'case 3: no price' => [
                'data' => [
                    'name' => ProductHelper::TEST_PRODUCT_NAME,
                ],
            ],
            'case 4: negative price' => [
                'data' => [
                    'name' => ProductHelper::TEST_PRODUCT_NAME,
                    'price' => - ProductHelper::TEST_PRODUCT_PRICE,
                ]
            ],
            'case 5: duplication' => [
                'data' => [
                    'name' => ProductHelper::TEST_PRODUCT_NAME,
                    'price' => ProductHelper::TEST_PRODUCT_PRICE,
                ]
            ],
            'case 6: too long name' => [
                'data' => [
                    'name' => str_repeat('n', 255),
                    'price' => ProductHelper::TEST_PRODUCT_PRICE,
                ]
            ],
            'case 7: invalid price type' => [
                'data' => [
                    'name' => ProductHelper::TEST_PRODUCT_NAME,
                    'price' => ProductHelper::TEST_PRODUCT_NAME,
                ]
            ],
        ];
    }

    public function testCreateActionReturnsUnauthorized(): void
    {
        $response = $this->performRequest('POST', 'app.products.create', [], [], false);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testUpdateActionSucceeds(): void
    {
        $this->productHelper->removeProduct(['name' => 'faulty string']);
        $this->productHelper->removeProduct();
        $product = $this->productHelper->createProduct('faulty string', 5559.995);

        $response = $this->performRequest(
            'PUT',
            'app.products.update',
            [
                'id' => $product->getId()
            ],
            [
                'name' => ProductHelper::TEST_PRODUCT_NAME,
                'price' => ProductHelper::TEST_PRODUCT_PRICE,
            ]
        );

        $responseContent = json_decode($response->getContent());

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_OK,
                'content' => [
                    'id' => $product->getId(),
                    'name' => ProductHelper::TEST_PRODUCT_NAME,
                    'price' => ProductHelper::TEST_PRODUCT_PRICE,
                    'created_at' => 'exists',
                    'updated_at' => 'exists',
                ]
            ],
            [
                'status_code' => $response->getStatusCode(),
                'content' => [
                    'id' => $responseContent->id,
                    'name' => $responseContent->name,
                    'price' => $responseContent->price,
                    'created_at' => $responseContent->created_at ? 'exists' : 'is missing',
                    'updated_at' => $responseContent->updated_at ? 'exists' : 'is missing',
                ]
            ]
        );
    }

    public function testUpdateActionReturnsNotFound(): void
    {
        $this->productHelper->removeProduct(['id' => 2077]);

        $response = $this->performRequest(
            'PUT',
            'app.products.update',
            [
                'id' => 2077
            ],
            [
                'name' => ProductHelper::TEST_PRODUCT_NAME,
                'price' => ProductHelper::TEST_PRODUCT_PRICE,
            ]
        );

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @dataProvider dataTestUpdateActionReturnsBadRequest
     * @param array $data
     */
    public function testUpdateActionReturnsBadRequest(array $data): void
    {
        $product = $this->productHelper->createProduct('faulty name', '99999');

        $response = $this->performRequest('PUT', 'app.products.update', ['id' => $product->getId()], $data);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @return array
     */
    public function dataTestUpdateActionReturnsBadRequest() : array
    {
        return [
            'case 1: no parameters' => [
                'data' => [],
            ],
            'case 2: no name' => [
                'data' => [
                    'price' => ProductHelper::TEST_PRODUCT_PRICE,
                ],
            ],
            'case 3: no price' => [
                'data' => [
                    'name' => ProductHelper::TEST_PRODUCT_NAME,
                ],
            ],
            'case 4: negative price' => [
                'data' => [
                    'name' => ProductHelper::TEST_PRODUCT_NAME,
                    'price' => - ProductHelper::TEST_PRODUCT_PRICE,
                ]
            ],
            'case 5: duplication' => [
                'data' => [
                    'name' => ProductHelper::TEST_PRODUCT_NAME,
                    'price' => ProductHelper::TEST_PRODUCT_PRICE,
                ]
            ],
            'case 6: too long name' => [
                'data' => [
                    'name' => str_repeat('n', 256),
                    'price' => ProductHelper::TEST_PRODUCT_PRICE,
                ]
            ],
        ];
    }

    public function testUpdateActionReturnsUnauthorized(): void
    {
        $response = $this->performRequest('PUT', 'app.products.update', ['id' => 2077], [], false);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testDeleteActionSucceeds(): void
    {
        $product = $this->productHelper->createProduct();

        $response =  $this->performRequest('DELETE', 'app.products.delete', ['id' => $product->getId()]);

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_NO_CONTENT,
                'removed' => true,
            ],
            [
                'status_code' => $response->getStatusCode(),
                'removed' => !(bool)$this->entityManager->find(Product::class, 2077)
            ]
        );
    }

    public function testDeleteActionReturnsNotFound(): void
    {
        $this->productHelper->removeProduct(['id' => 2077]);

        $response = $this->performRequest('DELETE', 'app.products.delete', ['id' => 2077]);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testDeleteActionReturnsUnauthorized(): void
    {
        $response = $this->performRequest('DELETE', 'app.products.delete', ['id' => 2077], [], false);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
