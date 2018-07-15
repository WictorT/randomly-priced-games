<?php
namespace App\Handler;

use App\DTO\BaseDTO;
use App\Entity\BaseEntity;
use App\Repository\BaseRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class BaseHandler
{
    /** @var UrlGeneratorInterface */
    private $router;

    /**
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router) {
        $this->router = $router;
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getRepository()->getQueryBuilder();
        $adapter = new DoctrineORMAdapter($queryBuilder);

        $paginator = (new Pagerfanta($adapter))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($page);

        $pageResults = $paginator->getCurrentPageResults();

        return [
            "page" => $paginator->getCurrentPage(),
            "per_page" => $paginator->getMaxPerPage(),
            "page_count" => count($pageResults),
            "total_pages" => $paginator->getNbPages(),
            "total_count" => $paginator->getNbResults(),
            "links" => $this->getPaginationLinks($paginator),
            'data' => $pageResults
        ];
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

    /**
     * @param BaseDTO $dto
     * @return BaseEntity
     */
    abstract public function create(BaseDTO $dto): BaseEntity;

    /**
     * @param BaseEntity $entity
     * @param BaseDTO $dto
     * @return BaseEntity
     */
    abstract public function update(BaseEntity $entity, BaseDTO $dto): BaseEntity;

    /**
     * @return BaseRepository
     */
    abstract public function getRepository(): BaseRepository;
}
