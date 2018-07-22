<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends ApiTestCase
{
    const DEFAULT_PER_PAGE = 3;
    const DEFAULT_PAGE = 1;

    public function testIndexActionSuccess()
    {
        $this->unauthorizedClient->request(
            'GET',
            $this->router->generate('app.products.list')
        );

        $response = $this->unauthorizedClient->getResponse();
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
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => 'Cyberpunk 2077']);
        if (!$product) {
            $product = (new Product)
                ->setName('Cyberpunk 2077')
                ->setPrice(59.99);
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }

        $this->unauthorizedClient->request(
            'GET',
            $this->router->generate('app.products.get', ['id' => $product->getId()])
        );

        $response = $this->unauthorizedClient->getResponse();
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
        // Try to remove product with id 2077 to induce NotFoundHttpException
        $product = $this->entityManager->find(Product::class, 2077);
        $product && $this->entityManager->remove($product);
        $this->entityManager->flush();

        $this->unauthorizedClient->request(
            'GET',
            $this->router->generate('app.products.get', ['id' => 2077])
        );

        $response = $this->unauthorizedClient->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCreateActionSucceeds()
    {
        // remove product with name=user@mail.com or username=user to avoid conflict
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => 'Cyberpunk 2077']);
        $product && $this->entityManager->remove($product);
        $this->entityManager->flush();

        $this->authorizedClient->request(
            'POST',
            $this->router->generate('app.products.create'),
            [],
            [],
            [],
            json_encode([
                'name' => 'Cyberpunk 2077',
                'price' => '59.99',
            ])
        );

        $response = $this->authorizedClient->getResponse();
        $responseContent = json_decode($response->getContent());
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => 'Cyberpunk 2077']);

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_CREATED,
                'content' => [
                    'id' => $product->getId(),
                    'name' => 'Cyberpunk 2077',
                    'price' => '59.99',
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
    public function testCreateActionReturnsBadRequest(array $data)
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
        ];
    }

    public function testCreateActionReturnsUnauthorized()
    {
        $this->unauthorizedClient->request(
            'POST',
            $this->router->generate('app.products.create')
        );

        $response = $this->unauthorizedClient->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testUpdateActionSucceeds()
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => 'Cyberpunk 2077']);
        if (!$product) {
            $product = (new Product)
                ->setName('faulty string')
                ->setPrice(5559.995);
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }

        $this->authorizedClient->request(
            'PATCH',
            $this->router->generate('app.products.update', ['id' => $product->getId()]),
            [],
            [],
            [],
            json_encode([
                'name' => 'Cyberpunk 2077',
                'price' => '59.99',
            ])
        );

        $response = $this->authorizedClient->getResponse();
        $responseContent = json_decode($response->getContent());
        $this->entityManager->refresh($product);

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_OK,
                'content' => [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
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
        // Try to remove product with id 2077 to induce NotFoundHttpException
        $product = $this->entityManager->find(Product::class, 2077);
        $product && $this->entityManager->remove($product);
        $this->entityManager->flush();

        $this->authorizedClient->request(
            'PATCH',
            $this->router->generate('app.products.update', ['id' => 2077]),
            [],
            [],
            [],
            json_encode([
                'name' => 'Cyberpunk 2077',
                'price' => '59.99',
            ])
        );

        $response = $this->authorizedClient->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testUpdateActionReturnsBadRequest()
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => 'Cyberpunk 2077']);
        if (!$product) {
            $product = (new Product)
                ->setName('Cyberpunk 2077')
                ->setPrice(59.99);
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }

        $this->authorizedClient->request(
            'PATCH',
            $this->router->generate('app.products.update', ['id' => $product->getId()])
        );

        $response = $this->authorizedClient->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testUpdateActionReturnsUnauthorized()
    {
        $this->unauthorizedClient->request(
            'PATCH',
            $this->router->generate('app.products.update', ['id' => 2077])
        );

        $response = $this->unauthorizedClient->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testDeleteActionSucceeds()
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => 'Cyberpunk 2077']);
        if (!$product) {
            $product = (new Product)
                ->setName('Cyberpunk 2077')
                ->setPrice(59.99);
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }

        $this->authorizedClient->request(
            'DELETE',
            $this->router->generate('app.products.delete', ['id' => $product->getId()])
        );

        $response = $this->authorizedClient->getResponse();

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
        // Try to remove product with id 2077 to induce NotFoundHttpException
        $product = $this->entityManager->find(Product::class, 2077);
        $product && $this->entityManager->remove($product);
        $this->entityManager->flush();

        $this->authorizedClient->request(
            'DELETE',
            $this->router->generate('app.products.delete', ['id' => 2077])
        );

        $response = $this->authorizedClient->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testDeleteActionReturnsUnauthorized()
    {
        $this->unauthorizedClient->request(
            'DELETE',
            $this->router->generate('app.products.delete', ['id' => 2077])
        );

        $response = $this->unauthorizedClient->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
