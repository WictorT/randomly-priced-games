<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserControllerTest extends ApiTestCase
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    protected function setUp()
    {
        parent::setUp();

        $this->encoder = static::$container->get('security.password_encoder');
    }

    public function testSignUpActionSucceeds()
    {
        // remove users with email=user@mail.com or username=user to avoid conflict
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['username' => 'user']);
        $user && $this->entityManager->remove($user);
        $user = $userRepository->findOneBy(['email' => 'user@mail.com']);
        $user && $this->entityManager->remove($user);
        $this->entityManager->flush();

        $response = $this->performRequest(
            'POST',
            'app.users.sign_up',
            [],
            [
                'username' => 'user',
                'email' => 'user@mail.com',
                'password' => 'user',
            ],
            false
        );
        $responseContent = json_decode($response->getContent());

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_CREATED,
                'content' => [
                    'username' => 'user',
                    'email' => 'user@mail.com',
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

    public function testSignUpActionReturnBadRequest()
    {
        // remove users with email=user@mail.com or username=user
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['username' => 'user']);
        $user && $this->entityManager->remove($user);
        $user = $userRepository->findOneBy(['email' => 'user@mail.com']);
        $user && $this->entityManager->remove($user);
        $this->entityManager->flush();

        // create user with email=user@mail.com or username=user to induce validation error
        $user = (new User)
            ->setUsername('user')
            ->setEmail('user@mail.com')
            ->setPassword('user');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $response = $this->performRequest(
            'POST',
            'app.users.sign_up',
            [],
            [
                'username' => 'user',
                'email' => 'user@mail.com',
                'password' => 'user',
            ],
            false
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
