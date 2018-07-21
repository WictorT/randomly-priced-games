<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTestCase extends WebTestCase
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    protected function setUp()
    {
        parent::setUp();
        static::bootKernel();

        $this->entityManager = static::$container->get('doctrine')->getManager();
    }

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
            ->setPassword('admin');

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
