<?php

namespace App\Tests;

use App\Tests\Helper\UserHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiTestCase extends WebTestCase
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var UserHelper
     */
    protected $userHelper;

    protected function setUp()
    {
        parent::setUp();
        static::bootKernel();

        $this->entityManager = static::$container->get('doctrine')->getManager();
        $this->userHelper = (new UserHelper($this->entityManager));
        $this->router = static::$container->get('router');

        $this->userHelper->guaranteeAdminUserExists();
    }

    /**
     * @param string $method
     * @param string $routeKey
     * @param array $routeParameters
     * @param array $requestBody
     * @param bool $authorized
     *
     * @return Response
     */
    protected function performRequest(
        string $method,
        string $routeKey,
        array $routeParameters = [],
        array $requestBody = [],
        $authorized = true
    ): Response {
        $client = static::createClient();
        $client->setServerParameter('CONTENT_TYPE', 'application/json');
        $authorized && $client->setServerParameter('HTTP_AUTHORIZATION', $this->getAccessTokenHeader());

        $client->request(
            $method,
            $this->router->generate($routeKey, $routeParameters),
            [],
            [],
            [],
            json_encode($requestBody)
        );

        return $client->getResponse();
    }

    /**
     * @return string
     */
    protected function getAccessTokenHeader()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'username' => 'admin',
                'email' => 'admin@mail.com',
                'password' => 'admin',
            ])
        );
        $responseContent = json_decode($client->getResponse()->getContent());

        return 'Bearer ' . $responseContent->token;
    }
}
