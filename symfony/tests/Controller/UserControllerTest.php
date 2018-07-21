<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserControllerTest extends WebTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    protected function setUp()
    {
        $container = self::bootKernel()->getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->encoder = $container->get('security.password_encoder');
    }

    public function testSignUpActionSuccess()
    {
        // remove users with email=admin@mail.com or username=admin to avoid conflict
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $user && $this->entityManager->remove($user);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@mail.com']);
        $user && $this->entityManager->remove($user);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/sign-up',
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

        $response = $client->getResponse();
        $responseContent = json_decode($response->getContent());

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_CREATED,
                'content' => [
                    'username' => 'admin',
                    'email' => 'admin@mail.com',
                    'cart_items' => [],
                    'password' => 'exists',
                    'created_at' => 'exists',
                    'updated_at' => 'exists',
                ]
            ],
            [
                'status_code' => $response->getStatusCode(),
                'content' => [
                    'username' => $responseContent->username,
                    'email' => $responseContent->email,
                    'cart_items' => $responseContent->cart_items,
                    'password' => $responseContent->password ? 'exists' : 'is missing',
                    'created_at' => $responseContent->created_at ? 'exists' : 'is missing',
                    'updated_at' => $responseContent->updated_at ? 'exists' : 'is missing',
                ]
            ]
        );
    }

    public function testSignUpActionFails()
    {
        // remove users with email=admin@mail.com or username=admin
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $user && $this->entityManager->remove($user);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@mail.com']);
        $user && $this->entityManager->remove($user);
        $this->entityManager->flush();

        // create user with email=admin@mail.com or username=admin to induce validation error
        $user = (new User)
            ->setUsername('admin')
            ->setEmail('admin@mail.com')
            ->setPassword('admin');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/sign-up',
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

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }
}
