<?php
namespace App\Controller;

use App\Entity\Product;
use App\Handler\ProductHandler;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /** @var ProductHandler $productHandler */
    private $productHandler;

    /**
     * @param ProductHandler $productHandler
     */
    public function __construct(ProductHandler $productHandler)
    {
        $this->productHandler = $productHandler;
    }

    /**
     * @Rest\Get(path="/products", name="app.products.list")
     *
     * @Rest\QueryParam(name="page", nullable=true, requirements="[1-9][0-9]*", strict=true, description="page", default="1")
     * @Rest\QueryParam(name="per_page", nullable=true, requirements="[1-9][0-9]*", strict=true, description="products per page", default="3")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return JsonResponse
     */
    public function indexAction(ParamFetcherInterface $paramFetcher)
    {
        $products = $this->productHandler->getPaginated(
            $paramFetcher->get('page'),
            $paramFetcher->get('per_page')
        );

        return $this->json($products, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(path="/products/{id}", name="app.products.get", requirements={"id":"\d+"})
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function getAction(Product $product)
    {
        return $this->json($product, Response::HTTP_OK);
    }
}
