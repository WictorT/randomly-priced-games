<?php
namespace App\Controller;

use App\DTO\ProductDTO;
use App\Entity\Product;
use App\Handler\ProductHandler;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

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

    /**
     * @Rest\Post(path="/products", name="app.products.create")
     * @ParamConverter("productDTO", converter="fos_rest.request_body")
     *
     * @param ProductDTO $productDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function createAction(ProductDTO $productDTO, ConstraintViolationListInterface $validationErrors)
    {
        if ($validationErrors->count() > 0) {
            throw new BadRequestHttpException($validationErrors);
        }

        $product = $this->productHandler->create($productDTO);

        return $this->json($product, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Patch(path="/products/{id}", name="app.products.update", requirements={"id":"\d+"})
     * @ParamConverter("productDTO", converter="fos_rest.request_body")
     *
     * @param Product $product
     * @param ProductDTO $productDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function updateAction(
        Product $product,
        ProductDTO $productDTO,
        ConstraintViolationListInterface $validationErrors
    ) {
        if ($validationErrors->count() > 0) {
            throw new BadRequestHttpException($validationErrors);
        }

        $product = $this->productHandler->update($product, $productDTO);

        return $this->json($product, Response::HTTP_CREATED);
    }
}
