<?php
namespace App\Controller;

use App\DTO\BaseDTO;
use App\DTO\ProductDTO;
use App\Entity\Product;
use App\Handler\ProductHandler;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @Rest\Route("/api")
 */
class ProductController extends FOSRestController
{
    /**
     * @var ProductHandler
     */
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
     * @Rest\QueryParam(
     *     name="page",
     *     nullable=true,
     *     requirements="[1-9][0-9]*",
     *     strict=true,
     *     description="page",
     *     default="1"
     * )
     * @Rest\QueryParam(
     *     name="per_page",
     *     nullable=true,
     *     requirements="[1-9][0-9]*",
     *     strict=true,
     *     description="products per page",
     *     default="3"
     * )
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return View
     */
    public function indexAction(ParamFetcherInterface $paramFetcher): View
    {
        $products = $this->productHandler->getPaginated(
            $paramFetcher->get('page'),
            $paramFetcher->get('per_page')
        );

        return View::create($products, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(path="/products/{id}", name="app.products.get", requirements={"id":"\d+"})
     *
     * @param Product $product
     *
     * @return View
     */
    public function getAction(Product $product): View
    {
        $productDto = $this->productHandler->getDto($product);

        return View::create($productDto, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(path="/products", name="app.products.create")
     * @ParamConverter("productDTO", converter="fos_rest.request_body")
     *
     * @param BaseDTO|ProductDTO $productDTO
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @throws BadRequestHttpException
     *
     * @return View
     */
    public function createAction(ProductDTO $productDTO, ConstraintViolationListInterface $validationErrors): View
    {
        $this->productHandler->handleValidationErrors($validationErrors);

        $productDTO = $this->productHandler->create($productDTO);

        return View::create($productDTO, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put(path="/products/{id}", name="app.products.update", requirements={"id":"\d+"})
     * @ParamConverter("productDTO", converter="fos_rest.request_body")
     *
     * @param Product $product
     * @param BaseDTO|ProductDTO $productDTO
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @throws BadRequestHttpException
     *
     * @return View
     */
    public function updateAction(
        Product $product,
        ProductDTO $productDTO,
        ConstraintViolationListInterface $validationErrors
    ): View {
        $this->productHandler->handleValidationErrors($validationErrors);

        $productDTO = $this->productHandler->update($product, $productDTO);

        return View::create($productDTO, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(path="/products/{id}", name="app.products.delete", requirements={"id":"\d+"})
     *
     * @param Product $product
     *
     * @return View
     */
    public function deleteAction(Product $product): View
    {
        $this->productHandler->delete($product);

        return View::create(null, Response::HTTP_NO_CONTENT);
    }
}
