<?php
namespace App\Controller;

use App\Handler\ProductHandler;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route(path="/products", name="app.products.list", methods={"GET"})
     *
     * @Rest\QueryParam(name="page", nullable=true, requirements="[1-9][0-9]*", strict=true, description="page", default="1")
     * @Rest\QueryParam(name="per_page", nullable=true, requirements="[1-9][0-9]*", strict=true, description="products per page", default="3")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return JsonResponse
     */
    public function index(ParamFetcherInterface $paramFetcher)
    {
        $products = $this->productHandler->getPaginated(
            $paramFetcher->get('page'),
            $paramFetcher->get('per_page')
        );

        return $this->json($products, Response::HTTP_OK);
    }
}
