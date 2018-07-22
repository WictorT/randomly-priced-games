<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends ApiTestCase
{
    const DEFAULT_PER_PAGE = 3;
    const DEFAULT_PAGE = 1;
    const TEST_PRODUCT_PRICE = 59.99;
    const TEST_PRODUCT_NAME = 'Cyberpunk 2077';

    protected function setUp()
    {
        parent::setUp();

        $this->createProduct();
    }

    public function testIndexActionSuccess()
    {
        $response = $this->performRequest('GET', 'app.products.list', [], [], false);
        $responseContent = json_decode($response->getContent());

        $products = $this->entityManager->getRepository(Product::class)->findAll();
        $productsCount = count($products);
        $pagesCount = (int)($productsCount / self::DEFAULT_PER_PAGE) + (bool)($productsCount % self::DEFAULT_PER_PAGE);
        $pageCount = min(self::DEFAULT_PER_PAGE, $productsCount);

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_OK,
                'content' => [
                    'page' => self::DEFAULT_PAGE,
                    'per_page' => self::DEFAULT_PER_PAGE,
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
                    'data_count' => count($responseContent->data),
                ]
            ]
        );
    }

    public function testGetActionSuccess()
    {
        $product = $this->createProduct();

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

    public function testGetActionReturnsNotFound()
    {
        $this->removeProduct(['id' => 2077]);

        $response = $this->performRequest('GET', 'app.products.get', ['id' => 2077], [], false);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCreateActionSucceeds()
    {
        $this->removeProduct();

        $response = $this->performRequest(
            'POST',
            'app.products.create',
            [],
            [
                'name' => self::TEST_PRODUCT_NAME,
                'price' => self::TEST_PRODUCT_PRICE,
            ]
        );
        $responseContent = json_decode($response->getContent());
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => self::TEST_PRODUCT_NAME]);

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_CREATED,
                'content' => [
                    'id' => $product->getId(),
                    'name' => self::TEST_PRODUCT_NAME,
                    'price' => self::TEST_PRODUCT_PRICE,
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
     * @param null|\Closure $setup
     */
    public function testCreateActionReturnsBadRequest(array $data, $setup = null)
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
                    'price' => self::TEST_PRODUCT_PRICE,
                ],
            ],
            'case 3: no price' => [
                'data' => [
                    'name' => self::TEST_PRODUCT_NAME,
                ],
            ],
            'case 4: negative price' => [
                'data' => [
                    'name' => self::TEST_PRODUCT_NAME,
                    'price' => - self::TEST_PRODUCT_PRICE,
                ]
            ],
            'case 5: duplication' => [
                'data' => [
                    'name' => self::TEST_PRODUCT_NAME,
                    'price' => self::TEST_PRODUCT_PRICE,
                ]
            ],
            'case 6: too long name' => [
                'data' => [
                    'name' => '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
                    'price' => self::TEST_PRODUCT_PRICE,
                ]
            ],
            'case 7: invalid price type' => [
                'data' => [
                    'name' => self::TEST_PRODUCT_NAME,
                    'price' => self::TEST_PRODUCT_NAME,
                ]
            ],
        ];
    }

    public function testCreateActionReturnsUnauthorized()
    {
        $response = $this->performRequest('POST', 'app.products.create', [], [], false);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testUpdateActionSucceeds()
    {
        $this->removeProduct(['name' => 'faulty string']);
        $this->removeProduct();

        $product = (new Product)
            ->setName('faulty string')
            ->setPrice(5559.995);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $response = $this->performRequest(
            'PUT',
            'app.products.update',
            [
                'id' => $product->getId()
            ],
            [
                'name' => self::TEST_PRODUCT_NAME,
                'price' => self::TEST_PRODUCT_PRICE,
            ]
        );

        $responseContent = json_decode($response->getContent());

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_OK,
                'content' => [
                    'id' => $product->getId(),
                    'name' => self::TEST_PRODUCT_NAME,
                    'price' => self::TEST_PRODUCT_PRICE,
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

    public function testUpdateActionReturnsNotFound()
    {
        $this->removeProduct(['id' => 2077]);

        $response = $this->performRequest(
            'PUT',
            'app.products.update',
            [
                'id' => 2077
            ],
            [
                'name' => self::TEST_PRODUCT_NAME,
                'price' => self::TEST_PRODUCT_PRICE,
            ]
        );

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @dataProvider dataTestUpdateActionReturnsBadRequest
     * @param array $data
     */
    public function testUpdateActionReturnsBadRequest(array $data)
    {
        $product = $this->createProduct('faulty name', '99999');

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
                    'price' => self::TEST_PRODUCT_PRICE,
                ],
            ],
            'case 3: no price' => [
                'data' => [
                    'name' => self::TEST_PRODUCT_NAME,
                ],
            ],
            'case 4: negative price' => [
                'data' => [
                    'name' => self::TEST_PRODUCT_NAME,
                    'price' => - self::TEST_PRODUCT_PRICE,
                ]
            ],
            'case 5: duplication' => [
                'data' => [
                    'name' => self::TEST_PRODUCT_NAME,
                    'price' => self::TEST_PRODUCT_PRICE,
                ]
            ],
            'case 6: too long name' => [
                'data' => [
                    'name' => str_repeat('n', 256),
                    'price' => self::TEST_PRODUCT_PRICE,
                ]
            ],
        ];
    }

    public function testUpdateActionReturnsUnauthorized()
    {
        $response = $this->performRequest('PUT', 'app.products.update', ['id' => 2077], [], false);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testDeleteActionSucceeds()
    {
        $product = $this->createProduct();

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

    public function testDeleteActionReturnsNotFound()
    {
        $this->removeProduct(['id' => 2077]);

        $response = $this->performRequest('DELETE', 'app.products.delete', ['id' => 2077]);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testDeleteActionReturnsUnauthorized()
    {
        $response = $this->performRequest('DELETE', 'app.products.delete', ['id' => 2077], [], false);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @param string $name
     * @param float $price
     * @return Product|null|object
     */
    private function createProduct($name = self::TEST_PRODUCT_NAME, $price = self::TEST_PRODUCT_PRICE): Product
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => $name]);

        if ($product) {
            $product->setPrice($price);
            $this->entityManager->merge($product);
        } else {
            $product = (new Product)
                ->setName($name)
                ->setPrice($price);
            $this->entityManager->persist($product);
        }
        $this->entityManager->flush();

        return $product;
    }

    /**
     * @param array $findParams
     * @return void
     */
    private function removeProduct(array $findParams = ['name' => self::TEST_PRODUCT_NAME])
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy($findParams);
        $product && $this->entityManager->remove($product);

        $this->entityManager->flush();
    }
}
