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
        $this->client->request(
            'GET',
            $this->router->generate('app.products.list')
        );

        $response = $this->client->getResponse();
        $responseContent = json_decode($response->getContent());

        $products = $this->entityManager->getRepository(Product::class)->findAll();
        $productsCount = count($products);
        $pagesCount = (int)($productsCount / self::DEFAULT_PER_PAGE) + 1;

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_OK,
                'content' => [
                    "page" => self::DEFAULT_PAGE,
                    "per_page" => self::DEFAULT_PER_PAGE,
                    "page_count" => min(self::DEFAULT_PER_PAGE, $productsCount),
                    "total_pages" => $pagesCount,
                    "total_count" => $productsCount,
                    "links" => [
                        "self" => $this->router->generate('app.products.list', ['page' => 1, 'per_page' => 3]),
                        "first" => $this->router->generate('app.products.list', ['page' => 1, 'per_page' => 3]),
                        "last" => $this->router->generate('app.products.list', ['page' => $pagesCount, 'per_page' => 3]),
                        "next" => $this->router->generate('app.products.list', ['page' => 2, 'per_page' => 3]),
                    ]
                ]
            ],
            [
                'status_code' => $response->getStatusCode(),
                'content' => [
                    "page" => $responseContent->page,
                    "per_page" => $responseContent->per_page,
                    "page_count" => $responseContent->page_count,
                    "total_pages" => $responseContent->total_pages,
                    "total_count" => $responseContent->total_count,
                    "links" => [
                        "self" => $responseContent->links->self,
                        "first" => $responseContent->links->first,
                        "last" => $responseContent->links->last,
                        "next" => $responseContent->links->next,
                    ]
                ]
            ]
        );
    }
}
