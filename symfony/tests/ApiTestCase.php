<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
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
     * @var Client
     */
    protected $unauthorizedClient;

    /**
     * @var Client
     */
    protected $authorizedClient;

    protected function setUp()
    {
        parent::setUp();
        static::bootKernel();

        $this->entityManager = static::$container->get('doctrine')->getManager();
        $this->router = static::$container->get('router');
        // TODO separate unauthorizaed client
        $this->unauthorizedClient = static::createClient(
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $this->authorizedClient = static::createClient(
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => $this->getAccessTokenHeader(),
            ]
        );
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
            $this->router->generate($routeKey),
            $routeParameters,
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
        $this->createAdminUser();

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

    private function createAdminUser(): void
    {
        // remove users with email=admin@mail.com or username=admin to avoid conflict
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['username' => 'admin']);
        $user && $this->entityManager->remove($user);
        $user = $userRepository->findOneBy(['email' => 'user@mail.com']);
        $user && $this->entityManager->remove($user);
        $this->entityManager->flush();

        // create user with email=admin@mail.com or username=admin to induce validation error
        $user = (new User)
            ->setUsername('admin')
            ->setEmail('admin@mail.com')
            ->setPassword('$2a$08$jHZj/wJfcVKlIwr5AvR78euJxYK7Ku5kURNhNx.7.CSIJ3Pq6LEPC');

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
