<?php
namespace App\Handler;

use App\DTO\BaseDTO;
use App\DTO\ProductDTO;
use App\Entity\BaseEntity;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Transformer\ProductTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\LessThan1CurrentPageException;
use Pagerfanta\Exception\LessThan1MaxPerPageException;
use Pagerfanta\Exception\NotIntegerCurrentPageException;
use Pagerfanta\Exception\NotIntegerMaxPerPageException;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductHandler extends BaseHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ProductTransformer
     */
    private $transformer;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ProductTransformer $transformer
     * @param UrlGeneratorInterface $router
     * @param ValidatorInterface $validator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ProductTransformer $transformer,
        UrlGeneratorInterface $router,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->transformer = $transformer;
        $this->router = $router;
        $this->validator = $validator;
    }

    /**
     * @param int $productId
     *
     * @throws NotFoundHttpException
     *
     * @return Product|null
     */
    public function getById(int $productId): ?Product
    {
        /** @var Product $product */
        $product = $this->getRepository()->find($productId);
        if ($product === null) {
            throw new NotFoundHttpException();
        }

        return $product;
    }

    /**
     * @param BaseEntity|Product $product
     *
     * @return BaseDTO|ProductDTO
     */
    public function getDto(BaseEntity $product): BaseDTO
    {
        return $this->transformer->transform($product);
    }

    /**
     * @param int $page
     * @param int $perPage
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     * @return array
     */
    public function getPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getRepository()->createQueryBuilder('p');
        $adapter = new DoctrineORMAdapter($queryBuilder);

        $paginator = new Pagerfanta($adapter);
        try {
            $paginator->setMaxPerPage($perPage);
        } catch (LessThan1MaxPerPageException|NotIntegerMaxPerPageException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        try {
            $paginator->setCurrentPage($page);
        } catch (OutOfRangeCurrentPageException|LessThan1CurrentPageException|NotIntegerCurrentPageException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $pageResults = $paginator->getCurrentPageResults();

        return [
            'page' => $paginator->getCurrentPage(),
            'per_page' => $paginator->getMaxPerPage(),
            'page_count' => \count($pageResults),
            'total_pages' => $paginator->getNbPages(),
            'total_count' => $paginator->getNbResults(),
            'links' => $this->getPaginationLinks($paginator),
            'data' => $this->transformer->transformMultiple($pageResults)
        ];
    }

    /**
     * @param BaseDTO|ProductDTO $productDto
     *
     * @return BaseDTO
     */
    public function create(BaseDTO $productDto): BaseDTO
    {
        $product = $this->transformer->reverseTransform($productDto);

        $validationErrors = $this->validator->validate($product);
        $this->handleValidationErrors($validationErrors);

        $this->entityManager->persist($product);
        $this->entityManager->flush();
        $this->entityManager->refresh($product);

        return $this->transformer->transform($product);
    }

    /**
     * @param BaseEntity $product
     * @param BaseDTO $productDto
     *
     * @return BaseDTO
     */
    public function update(BaseEntity $product, BaseDTO $productDto): BaseDTO
    {
        $product = $this->transformer->reverseTransform($productDto, $product);

        $validationErrors = $this->validator->validate($product);
        $this->handleValidationErrors($validationErrors);

        $this->entityManager->merge($product);
        $this->entityManager->flush();
        $this->entityManager->refresh($product);

        return $this->transformer->transform($product);
    }

    /**
     * @param BaseEntity|Product $product
     *
     * @return void
     */
    public function delete(BaseEntity $product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    /**
     * @return ProductRepository
     */
    public function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Product::class);
    }

    /**
     * @param Pagerfanta $paginator
     * @return array
     */
    private function getPaginationLinks(Pagerfanta $paginator): array
    {
        $links = [];

        $links['self'] = $this->router->generate(
            'app.products.list',
            [
                'page' => $paginator->getCurrentPage(),
                'per_page' => $paginator->getMaxPerPage()
            ]
        );

        $links['first'] = $this->router->generate(
            'app.products.list',
            [
                'page' => 1,
                'per_page' => $paginator->getMaxPerPage()
            ]
        );

        $links['last'] = $this->router->generate(
            'app.products.list',
            [
                'page' => $paginator->getNbPages(),
                'per_page' => $paginator->getMaxPerPage()
            ]
        );

        $paginator->hasPreviousPage() && $links['previous'] = $this->router->generate(
            'app.products.list',
            [
                'page' => $paginator->getCurrentPage() - 1,
                'per_page' => $paginator->getMaxPerPage()
            ]
        );

        $paginator->hasNextPage() && $links['next'] = $this->router->generate(
            'app.products.list',
            [
                'page' => $paginator->getCurrentPage() + 1,
                'per_page' => $paginator->getMaxPerPage()
            ]
        );

        return $links;
    }
}
